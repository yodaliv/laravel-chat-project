<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Naire;
use App\Question;
use App\Option;
use App\Answer;
use App\Http\Requests;

class NaireInfo
{
    public $id;
    public $content;
}

class Naire2 extends NaireInfo
{
    public $questions;
}

class Question2
{
    public $content;
    public $options;
}

class NaireWithResult extends Naire2
{
    public $result;
}

class UserController extends Controller
{
    //
    public function index()
    {
        $id = Auth::guard('api')->user()->id;
        $user = Auth::guard('api')->user();

        $naireList = array();
        $naires = $user->naires()->get();

        foreach($naires as $cur)
        {
            $curInfo = new NaireInfo;
            $curInfo->id = $cur->id;
            $curInfo->content = $cur->content;
            array_push($naireList, $curInfo);
        }
        return response()->json($naireList, 200);
    }

    public function result(Request $request)
    {
        $user = Auth::guard('api')->user();
        $id = $request->json('id');

        $naire = Naire::find($id);
        $questions = $naire->questions()->get();
        $new_naire = new NaireWithResult;
        $new_naire->id = $naire->id;
        $new_naire->content = $naire->content;
        $new_naire->questions = array();
        $new_naire->result = array();

        foreach($questions as $question)
        {
            $new_question = new Question2;
            $new_question->content = $question->content;
            $new_question->options = array();

            $options = $question->options()->get();

            $resultForOption = array();

            foreach ($options as $option)
            {
                array_push($new_question->options, $option->content);
                $ans = $option->answer()->get()->first();
                // echo $ans->id; exit;
                array_push($resultForOption, $ans->value);
            }

            array_push($new_naire->questions, $new_question);
            array_push($new_naire->result, $resultForOption);
        }

        return response()->json($new_naire, 200);
    }
}
