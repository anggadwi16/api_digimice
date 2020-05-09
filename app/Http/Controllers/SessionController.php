<?php

namespace App\Http\Controllers;
use App\Session;
use App\EventAgenda;
use App\Materi;
use Illuminate\Http\Request;
class SessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index($id){
        $session = Session::where('event_id',$id)->get();
        $array = [];
        foreach ($session as $value) {
            $array[] = [
                'id' => $value->id,
                'name' => $value->name,
                'agenda' => $value->agenda
            ];
        }
        
        return response($array);
    }

    public function search(Request $request){
        $event_id = $request->input('event_id');
        $search = $request->input('search');
        $session = Session::where('event_id',$event_id)->where('name','LIKE','%'.$search.'%')->get();
        $array = [];
        foreach ($session as $value) {
            $array[] = [
                'id' => $value->id,
                'name' => $value->name,
                'agenda' => $value->agenda
            ];
        }
        
        return response($array);
    }

    public function create(Request $request){

        $input = $request->all();

        Session::create($input);

        return "berhasil";
    }

    public function edit(Request $request, $id){
        $session = Session::findOrFail($id);
        $input = $request->all();
        $session->update($input);

        return "berhasil";
    }

    public function show($id){
        $array = [];
        $session = Session::findOrFail($id);
        $array = [
            $session
        ];
        return $array;
    }

    public function delete($id){
        
        if($agenda = EventAgenda::where('event_session_id',$id)->get()){
            
            foreach ($agenda as $value) {

                if($materi = Materi::where('event_agenda_id',$value->id)->get()){
                    foreach ($materi as $value) {
                        unlink($value->url);
                        $value->delete();    
                    }
                }
                $value->delete();
            }
        }
        
        $session = Session::findOrFail($id);
        $session->delete();
        return "Berhasil hapus";
    }
    //
}
