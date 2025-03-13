<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\AnnouncementLog;
use App\Models\Announcement;
use App\Rules\UniqueJsonValue;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        // $data = BundleEquiptment::all();
        // return Inertia::render('BundleEquiptment', ['data' => $data]);

        $templates = EmailTemplate::orderBy('id', 'desc')->paginate(10);
        return Inertia::render('Setup/EmailTemplate', ['templates' => $templates]);
    }
    public function store(Request $request)
    {
        $to_validate = array(
            ['key' => 'bill_invoice', 'name' => 'Bill Invoice'],
            ['key' => 'unpaid_reminder', 'name' => 'Unpaid Reminder'],
        );
        $table = 'email_templates';
        Validator::make($request->all(), [
            'name' => 'required|unique:email_templates,name|max:255',
            'body' => 'required|max:280',
            'default_name' => ['required',  new UniqueJsonValue($table, $request->id, $to_validate)],
        ])->validate();
        if ($request->default) {
            $type = ($request->type) ? 'email' : 'sms';
            EmailTemplate::where('type', '=', $type)->update(['default' => 0]);
        }
        $template = new EmailTemplate();
        $template->name = $request->name;
        $template->title = $request->title;
        $template->body = $request->body;
        $template->cc_email = $request->cc_email;
        $template->type = ($request->type) ? 'email' : 'sms';
        $template->is_bulk = ($request->is_bulk && !$request->type)?1:0;
        $template->default_name = json_encode($request->default_name);
        $template->save();
        return redirect()->back()->with('message', 'Template Created Successfully.');
    }
    public function update(Request $request)
    {
        $table = 'email_templates';
        $to_validate = array(
            ['key' => 'bill_invoice', 'name' => 'Bill Invoice'],
            ['key' => 'unpaid_reminder', 'name' => 'Unpaid Reminder'],
        );
        Validator::make($request->all(), [
            'name' => ['required'],
            'body' => ['required'],
            'default_name' => ['required',  new UniqueJsonValue($table, $request->id, $to_validate),]
        ])->validate();
        if ($request->has('id')) {
            if ($request->default) {
                $type = ($request->type) ? 'email' : 'sms';
                EmailTemplate::where('type', '=', $type)->update(['default' => 0]);
            }
            $template =  EmailTemplate::find($request->input('id'));
            $template->name = $request->name;
            $template->title = $request->title;
            $template->body = $request->body;
            $template->cc_email = $request->cc_email;
            $template->type = ($request->type) ? 'email' : 'sms';
            $template->is_bulk = ($request->is_bulk && !$request->type)?1:0;
            $template->default_name = json_encode($request->default_name);
            $template->save();
        }

        return redirect()->back()->with('message', 'Template Updated Successfully.');
    }
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            AnnouncementLog::where('template_id', '=', $request->input('id'))->delete();
            Announcement::where('template_id', '=', $request->input('id'))->delete();
            EmailTemplate::where('id', '=', $request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
