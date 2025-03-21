<?php

namespace App\Http\Controllers;

use App\Jobs\ReminderSMSJobNoInvoice;
use App\Models\BillReminder;
use App\Models\SmsGateway;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Auth;

class BillReminderController extends Controller
{
    /**
     * Display customers who need bill reminders
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
      
        $smsgateway = SmsGateway::first();
        $daysBeforeExpiry = (int)$request->input('days', 5);
        
        
    
        $query = BillReminder::query();

        // Apply filters if they exist
        if ($request->filled('filter_id')) {
            $query->where('ftth_id', 'like', '%' . $request->filter_id . '%');
        }

        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->filter_name . '%');
        }

        if ($request->filled('filter_phone')) {
            $query->where('phone_1', 'like', '%' . $request->filter_phone . '%');
        }

        if ($request->filled('filter_expiry')) {
            $startDate = Carbon::parse($request?->filter_expiry[0])->format('Y-m-d');
            $endDate =Carbon::parse($request?->filter_expiry[1])->format('Y-m-d');
  
            $query->where('service_off_date', '>=', $startDate);
            $query->where('service_off_date', '<=',  Carbon::parse($endDate)->endOfDay());
        }

        if ($request->has('filter_no_phone')) {
            $noPhoneValue = $request->filter_no_phone;
            // Handle string 'true'/'false' from query parameters
            if (is_string($noPhoneValue) && $noPhoneValue === 'true') {
                $noPhoneValue = true;
            }
            
            if ($noPhoneValue === true || $noPhoneValue === 1) {
                $query->where(function ($query) {
                    $query->whereNull('phone_1')
                        ->orWhere('phone_1', '')
                        ->orWhereRaw("TRIM(phone_1) = ''");
                });
            }
        }
        
        $customers = $query->paginate(10);
        $customers->appends($request->all())->links();
        return Inertia::render('BillReminders/Index', [
            'smsgateway' => $smsgateway,
            'customers' => $customers,
            'daysBeforeExpiry' => $daysBeforeExpiry,
            'today' => Carbon::today()->format('Y-m-d'),
            'endDate' => Carbon::today()->addDays($daysBeforeExpiry)->format('Y-m-d')
        ]);
    }

    public function createReminder(Request $request)
    {
        $request->validate([
            'expiration' => 'required|array',
        ]);
        $expiration = $request->input('expiration');

        BillReminder::getCustomersForReminder($expiration);
        return redirect()->route('bill-reminders.index')
            ->with('message', 'Bill reminder created successfully.');
    }
    public function emptyReminder(Request $request)
    {
        BillReminder::truncate();
        return redirect()->route('bill-reminders.index')
            ->with('message', 'Bill reminder empty successfully.');
    }
    /**
     * Send reminders to selected customers
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendReminders(Request $request)
    {
        $customerIds = $request->input('customer_ids', []);
        $unsentIds =    BillReminder::whereIn('id', $customerIds)
            ->where(function ($query) {
                $query->whereNotNull('phone_1')
                    ->whereRaw("TRIM(phone_1) <> ''");
            })
            ->where(function ($query) {
                $query->whereNull('sms_sent_at')
                    ->orWhereRaw("TRIM(sms_sent_at) = ''");
            })
            ->where('sms_status', 'pending')
            ->pluck('id')->toArray();
        if ($unsentIds) {

            $oneTimeCode = strtoupper(uniqid());

            BillReminder::whereIn('id', $unsentIds)->update(['onetimecode' => $oneTimeCode]);
        }
        // Here you would implement the actual sending of reminders
        // This could be via email, SMS, etc.
        foreach ($unsentIds as $customerId) {
            // Send reminder to customer with ID $customerId
            // This could be via email, SMS, etc.
            // For now, we'll just log it
            dispatch(new ReminderSMSJobNoInvoice($customerId, count($unsentIds), Auth::id(), $oneTimeCode));
            \Log::info("Reminder sent to customer with ID {$customerId}");
        }

        return redirect()->route('bill-reminders.index')
            ->with('message', count($customerIds) . ' reminders have been queued for sending.');
    }
    public function sendAllReminders()
    {
        $customerIds = BillReminder::pluck('id')->toArray();
        $unsentIds =    BillReminder::whereIn('id', $customerIds)
        ->where(function ($query) {
            $query->whereNotNull('phone_1')
                ->whereRaw("TRIM(phone_1) <> ''");
        })
        ->where(function ($query) {
            $query->whereNull('sms_sent_at')
                ->orWhereRaw("TRIM(sms_sent_at) = ''");
        })
        ->where('sms_status', 'pending')
        ->pluck('id')->toArray();
        if ($unsentIds) {
            $oneTimeCode = strtoupper(uniqid());
            BillReminder::whereIn('id', $unsentIds)->update(['onetimecode' => $oneTimeCode]);
        }
        // Here you would implement the actual sending of reminders
        // This could be via email, SMS, etc.
        foreach ($unsentIds as $customerId) {
            // Send reminder to customer with ID $customerId
            // This could be via email, SMS, etc.
            // For now, we'll just log it
            dispatch(new ReminderSMSJobNoInvoice($customerId, count($unsentIds), Auth::id(), $oneTimeCode));
            \Log::info("Reminder sent to customer with ID {$customerId}");
        }

        return redirect()->route('bill-reminders.index')
            ->with('message', count($customerIds) . ' reminders have been queued for sending.');
    }
    /**
     * Update the specified bill reminder in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ftth_id' => 'required|string|max:255',
            'phone_1' => 'required|string|max:20',
            'service_off_date' => 'required|date',
        ]);

        $reminder = BillReminder::findOrFail($id);

        $reminder->update([
            'name' => $request->name,
            'ftth_id' => $request->ftth_id,
            'phone_1' => $request->phone_1,
            'service_off_date' => $request->service_off_date,
        ]);

        // If reset SMS is checked, reset the SMS status
        if ($request->reset_sms) {
            $reminder->update([
                'sms_status' => 'pending',
                'onetimecode' => null,
                'sms_sent_at' => null
            ]);
        }

        return redirect()->route('bill-reminders.index')
            ->with('message', 'Bill reminder updated successfully.');
    }
    public function clearNoPhoneCustomers()
    {
        // Delete all bill reminders where phone_1 is null, empty, or contains only whitespace
        $deleted = BillReminder::where(function ($query) {
            $query->whereNull('phone_1')
                ->orWhere('phone_1', '')
                ->orWhereRaw("TRIM(phone_1) = ''");
        })->delete();

        return redirect()->route('bill-reminders.index')
            ->with('message', $deleted . ' customers without phone numbers have been removed from the reminder list.');
    }
    public function destroy($id)
    {
        $reminder = BillReminder::findOrFail($id);
        $reminder->delete();

        return redirect()->route('bill-reminders.index')
            ->with('message', 'Bill reminder deleted successfully.');
    }

    /**
     * Remove the specified bill reminders from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSelected(Request $request)
    {
        $customerIds = $request->input('customer_ids', []);

        if (!empty($customerIds)) {
            BillReminder::whereIn('id', $customerIds)->delete();
        }

        return redirect()->route('bill-reminders.index')
            ->with('message', count($customerIds) . ' Bill reminders deleted successfully.');
    }
}
