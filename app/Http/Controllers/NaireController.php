<?php

namespace App\Http\Controllers;

// use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Naire;
use App\Question;
use App\Option;
use App\Answer;
use App\Http\Requests;
use Auth;
// use Gate;

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

class NaireWithResult extends Naire
{
    public $result;
}

class NaireController extends Controller
{
    //

    public function index()
    {
        $questionnaires = Naire::orderBy('created_at', 'asc')->get();
        $nairelist = array();

        for ($i = 0; $i < count($questionnaires); $i++)
        {
            $naireInfo = new NaireInfo;
            $naireInfo->id = $questionnaires[$i]->id;
            $naireInfo->content = $questionnaires[$i]->content;
            array_push($nairelist, $naireInfo);
        }
        return response()->json($nairelist, 200);
    }

    public function naireById(Request $request, $id)
    {
        $naire = Naire::find($id);
        $questions = $naire->questions()->get();
        $new_naire = new Naire2;
        $new_naire->id = $naire->id;
        $new_naire->content = $naire->content;
        $new_naire->questions = array();

        // echo count($questionsOnDb); exit;

        foreach($questions as $question)
        {
            $new_question = new Question2;
            $new_question->content = $question->content;
            // echo $qu->content; exit;
            $new_question->options = array();

            $options = $question->options()->get();

            foreach ($options as $option)
            {
                array_push($new_question->options, $option->content);
            }

            array_push($new_naire->questions, $new_question);
        }

        return response()->json($new_naire, 200);
    }

    public function store(Request $request)
    {
        $id = $request->json('id');
        // echo $id; exit;
        $naire = Naire::find($id);
        $results = $request->json('result');

        $questions = $naire->questions()->get();

        for ($i = 0; $i < count($questions); $i++)
        {
            if ($results[$i] == -1)
            {
                continue;
            }

            $options = $questions[$i]->options()->get();

            $answer = $options[$results[$i]]->answer()->get()->first();
            $answer->value = 1 + $answer->value;
            $answer->save();
        }

        return response()->json('questionnaire has been saved successfully.', 200);     
    }

    public function append(Request $request)
    {
        $user = Auth::guard('api')->user();
        // if (Gate::forUser($user)->denies('create_questionnaire')) {
        //     return response()->json('Access Denied!', 501);
        // }

        // $this->validate($request, [
        //     'title' => 'bail|required|unique:questionnaires|min:3|max:255',
        //     // 'description' => 'required',
        // ]);
        $naire = $request->json('naire');

        $questionnaire = new Naire;
        $questionnaire->content = $naire['content'];
        $questionnaire->user_id = $user->id;
        $questionnaire->save();

        $id = $questionnaire->id;
        $questions = $naire['questions'];
        foreach($questions as $question) {
            $newQuestion = new Question;
            $newQuestion->content = $question['content'];
            $newQuestion->naire_id = $id;
            $newQuestion->save();

            $options = $question['options'];
            
            foreach($options as $option) {
                $newOption = new Option;
                $newOption->content = $option;
                $newOption->question_id = $newQuestion->id;
                $newOption->save();

                $newAnswer = new Answer;
                $newAnswer->option_id = $newOption->id;
                $newAnswer->value = 0;
                $newAnswer->save();
            }
        }

        return response()->json('questionnaire has been created successfully.', 200);
    }

    public function destroy($id)
    {
        $user = Auth::guard('api')->user();
        // if (Gate::forUser($user)->denies('delete_questionnaire')) {
        //     return response()->json('Access Denied!', 501);
        // }
        
        // $id = $request->json('id');

        $questionnaire = Naire::find($id);
        $questions = $questionnaire->questions()->get();

        foreach($questions as $question)
        {
            $options = $question->options()->get();
            foreach($options as $option)
            {
                $answer = $option->answer()->get()->first();
                $answer->delete();
                $option->delete();
            }
            $question->delete();
        }
        $questionnaire->delete();
        return response()->json('questionnaire has been deleted successfully.', 200);
    }
}
