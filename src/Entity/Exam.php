<?php

namespace App\Entity;

use App\Repository\ExamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExamRepository::class)
 */
class Exam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="exams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToMany(targetEntity=Question::class, inversedBy="exams")
     */
    private $questions;

    /**
     * @ORM\ManyToMany(targetEntity=Answer::class, inversedBy="exams")
     */
    private $answers;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="exams")
     */
    private $section;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
    public function setQuestions(Collection $questions)
    {
        $this->questions = $questions;
        return $this;
    }
    */

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        $this->questions->removeElement($question);

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        $this->answers->removeElement($answer);

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->completed;
    }

    public function setCompleted(?\DateTimeInterface $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getPoints(): ?float
    {
        if (!$this->getCompleted()) {
            return NULL;
        }
        $value = 0;
        $points = $this->getResultPoints();
        foreach ($points as $point) {
            $value += $point;
        }
        return $value;
    }

    public function getPointsPerQuestion()
    {
        return 100 / $this->getQuestions()->count();
    }

    public function getResultPoints()
    {
        $pointsByQuestions = [];
        foreach ($this->getQuestions() as $question) {
            $point = $this->calcPoints($question);
            $pointsByQuestions[$question->getId()] = $point;
        }
        return $pointsByQuestions;
    }

    public function getAnswersByQuestion(Question $question): Collection
    {
        return $this->getAnswers()->filter(function(Answer $answer) use ($question) {
            return $answer->getQuestion() instanceof Question
                && $answer->getQuestion()->getId() === $question->getId();
        });
    }

    protected function calcPoints(Question $question)
    {
        $answersByQuestion = $this->getAnswersByQuestion($question);
        $hasOnlyRight = $answersByQuestion->forAll(
            static function ($key, $value) {
                return $value->getRightAnswer();
        });
        if (!$hasOnlyRight) {
            return 0;
        }

        $rightAnswers = $question->getRightAnswers();
        $rightAnswersCount = $rightAnswers->count();

        $rightInAnswers = $answersByQuestion->filter(function(Answer $answer){
            return $answer->getRightAnswer();
        });
        $rightInAnswersCount = $rightInAnswers->count();

        if ($rightInAnswersCount === $rightAnswersCount) {
            return $this->getPointsPerQuestion();
        }

        return $this->getPointsPerQuestion() / $rightAnswersCount * $rightInAnswersCount;
    }

}
