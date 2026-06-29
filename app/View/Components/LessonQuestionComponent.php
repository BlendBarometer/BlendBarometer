<?php
namespace App\View\Components;

use Illuminate\View\Component;

class LessonQuestionComponent extends Component
{
    public $question;
    public $selectedAnswer;
    public $fieldName;
    public $description;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($question, $selectedAnswer, $fieldName, $description = null)
    {
        $this->question = $question;
        $this->selectedAnswer = $selectedAnswer;
        $this->fieldName = $fieldName;
        $this->description = $description;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.lesson-question-component');
    }
}
