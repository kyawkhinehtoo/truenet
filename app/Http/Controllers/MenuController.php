<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use App\Models\Role;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
class MenuController extends Controller
{
    public function index(Request $request){
        $menus = Menu::all();
        return Inertia::render('Setup/Menu',
         ['menus' => $menus]);
    }
    public function getMenu(Request $request){
        $role = Role::find($request->id);

        if($role->menu_id){
            $id = array();
            $menu_list = explode(",",$role->menu_id);
            foreach ($menu_list as $value) {
                array_push($id,$value);
            }
            $user_menu = Menu::whereIn('id',$id)
            ->get();
            return json_decode($user_menu,200);
        }
       
    }
    public function store(Request $request){
        Validator::make($request->all(), [
            'name' => ['required'],
            'display' => ['required'],
            'url' => ['required'],
        ])->validate();

        $menu = new Menu();
        $menu->name = $request->name;
        $menu->display = $request->display;
        $menu->type = $request->type;
        $menu->admin = $request->admin;
        $menu->url = $request->url;
        if($request->fa)
        $menu->fa = $request->fa;

        $menu->save();
        return redirect()->route('menu.index')->with('message', 'Menu Created Successfully.');
    }
    public function update(Request $request){
        Validator::make($request->all(), [
            'id' => ['required'],
            'name' => ['required'],
            'display' => ['required'],
            'url' => ['required'],
        ])->validate();

        $menu = Menu::find($request->id);
        $menu->name = $request->name;
        $menu->display = $request->display;
        $menu->type = $request->type;
        $menu->admin = $request->admin;
        $menu->url = $request->url;
        if($request->fa)
        $menu->fa = $request->fa;
        $menu->save();
        return redirect()->route('menu.index')->with('message', 'Menu Updated Successfully.');
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Menu::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Menu Deleted Successfully.');
        }
    }
}
