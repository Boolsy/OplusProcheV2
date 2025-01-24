<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::where('deleted', 0)->get();
        return view('admin.questions.index', compact('questions'));
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $question->update($request->only(['text', 'difficulty', 'category_id']));
        $question->corrected_by = auth()->id();
        $question->save();

        return redirect()->route('questions.index')->with('success', 'Question mise à jour.');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->deleted = 1;
        $question->save();

        return redirect()->route('questions.index')->with('success', 'Question supprimée.');
    }
}

