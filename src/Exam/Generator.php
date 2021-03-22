<?php

namespace App\Exam;

use App\Entity\Exam;
use App\Entity\Section;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class Generator
{
    protected $em;

    /**
     * The count of question.
     *
     * @var int
     */
    protected $question_count;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * Generator constructor.
     *
     * @param EntityManager $em
     * @param QuestionRepository $questionRepository
     * @param $question_count
     */
    public function __construct(EntityManager $em, QuestionRepository $questionRepository, $question_count)
    {
        $this->em = $em;
        $this->question_count = $question_count;
        $this->questionRepository = $questionRepository;
    }

    /**
     * Gets new Exam entity with questions.
     *
     * @param Section $section
     * @return Exam
     */
    public function doExam(Section $section)
    {
        $exam = new Exam();
        $exam->setSection($section);
        $exam->setCreated(new \DateTime());

        $postsIds = [];
        foreach ($section->getPosts() as $post) {
            $postsIds[] = $post->getId();
        }

        $questions = $this->questionRepository->findByPostIds($postsIds);
        if (count($questions) < $this->question_count) {
            throw new \Exception('Not enough questions to pass the test');
        }
        shuffle($questions);
        $randQuestions = array_slice($questions, 0 , $this->question_count);
        foreach ($randQuestions as $question) {
            $exam->addQuestion($question);
        }

        return $exam;
    }



}

