<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\Post;
use App\Entity\Section;
use App\Entity\User;
use App\Exam\Evaluation;
use App\Exam\Generator;
use App\Form\CreateSimpleExamForm;
use App\Form\ExamFlow;
use App\Form\PostType;
use App\Form\SimpleExamForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ExamController
 *
 * @Route("/exam")
 * @IsGranted("ROLE_USER")
 * @package App\Controller
 */
class ExamController extends AbstractController
{

    /**
     * Exam form page.
     *
     * @Route("/{id<\d+>}", methods="GET|POST", name="exam_form")
     *
     * @param Section $section
     * @param Security $security
     * @param ExamFlow $flow
     * @param Generator $examGenerator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createExamAction(Section $section, Security $security, ExamFlow $flow, Generator $examGenerator) {
        try {
            $exam = $flow->getFormData();
        }
        catch (\RuntimeException $e) {
            $exam = $examGenerator->doExam($section); // Your form data class. Has to be an object, won't work properly with an array.
            /** @var User $student */
            $student = $security->getUser();
            $exam->setStudent($student);
        }


        $flowId = $flow->getId();
        $iid= $flow->getInstanceId();
        $i = $flow->getInstanceKey();

        $flow->bind($exam);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished
                $em = $this->getDoctrine()->getManager();
                $em->persist($exam);
                $em->flush();

                $flow->reset(); // remove step data from the session

                return $this->redirect($this->generateUrl('homepage')); // redirect when done
            }
        }

        return $this->render('exam/create_exam.html.twig', [
            'form' => $form->createView(),
            'flow' => $flow,
        ]);
    }

    /**
     * @Route("/simple/create/{id<\d+>}", methods="GET|POST", name="simple_exam_create")
     *
     * @param Request $request
     * @param Section $section
     * @param Security $security
     * @param Generator $examGenerator
     */
    public function createSimpleExamAction(Request $request, Section $section, Security $security, Generator $examGenerator)
    {
        try {
            $exam = $examGenerator->doExam($section);
        }catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('section.canonical', [
                'slug' => $section->getSlug(),
            ]);
        }

        $exam->setStudent($this->getUser());

        $form = $this->createForm(CreateSimpleExamForm::class, $exam)
            ->add('save', SubmitType::class, ['label' => 'Start Exam']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exam);
            $em->flush();

            // $this->addFlash('success', 'exam.created_successfully');


            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('simple_exam_go', [
                    'id' => $exam->getId(),
                ]);
            }

            return $this->redirectToRoute('main');
        }
        return $this->render('exam/create_simple_exam.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam,
        ]);
    }

    /**
     * @Route("/simple/go/{id<\d+>}", methods="GET|POST", name="simple_exam_go")
     *
     * @param Request $request
     * @param Exam $exam
     * @param Security $security
     * @param Generator $examGenerator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function simpleExamAction(Request $request, Exam $exam, Security $security, Generator $examGenerator)
    {
        if ($exam->getCompleted()) {
            return $this->redirectToRoute('simple_exam_result', [
                'id' => $exam->getId(),
            ]);
            // throw new AccessDeniedException('This test has already been completed');
        }

        $form = $this->createForm(SimpleExamForm::class, $exam)
            ->add('save', SubmitType::class, ['label' => 'Submit answers']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exam);
            $em->flush();

            // $this->addFlash('success', 'exam.go_successfully');
            // $this->addFlash('success', 'exam.final');


            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('simple_exam_result', [
                    'id' => $exam->getId(),
                ]);
            }

            return $this->redirectToRoute('main');
        }
        return $this->render('exam/simple_exam.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam,
        ]);
    }

    /**
     * @Route("/simple/result/{id<\d+>}", methods="GET|POST", name="simple_exam_result")
     *
     * @param Request $request
     * @param Exam $exam
     * @param Security $security
     */
    public function result(Request $request, Exam $exam, Security $security)
    {
        $points = $exam->getResultPoints();
        $fullPoints = 0;
        foreach ($points as $point) {
            $fullPoints += $point;
        }
        if (!$exam instanceof Exam) {
            throw new AccessDeniedException('The specified test does not exist');
        }
        if (!$exam->getCompleted()) {
            return $this->redirectToRoute('simple_exam_go', [
                'id' => $exam->getId(),
            ]);
        }
        return $this->render(
            'exam/result_exam.html.twig', [
            'exam' => $exam,
            'points' => $points,
            'full_points' => $fullPoints,
        ]);
    }
}

