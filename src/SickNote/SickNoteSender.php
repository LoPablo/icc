<?php

namespace App\SickNote;

use App\Converter\StudentStringConverter;
use App\Converter\UserStringConverter;
use App\Entity\GradeTeacher;
use App\Entity\User;
use App\Settings\SickNoteSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SickNoteSender {

    private $converter;
    private $twig;
    private $mailer;
    private $translator;
    private $dateHelper;
    private $userConverter;
    private $settings;

    public function __construct(StudentStringConverter $converter, Environment $twig, Swift_Mailer $mailer, TranslatorInterface $translator,
                                DateHelper $dateHelper, UserStringConverter $userConverter, SickNoteSettings $settings) {
        $this->converter = $converter;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->dateHelper = $dateHelper;
        $this->userConverter = $userConverter;
        $this->settings = $settings;
    }

    public function sendSickNote(SickNote $note, User $sender) {
        $cc = [ ];
        $teachers = [ ];

        /** @var GradeTeacher $teacher */
        foreach($note->getStudent()->getGrade()->getTeachers() as $teacher) {
            $teachers[] = $teacher->getTeacher();
            $cc[] = $teacher->getTeacher()->getEmail();
        }

        $isQuarantine = $note->getReason()->equals(SickNoteReason::Quarantine());

        $body = $this->twig->render('email/sick_note.html.twig', [
            'note' => $note,
            'teachers' => $teachers,
            'sender' => $this->userConverter->convert($sender),
            'now' => $this->dateHelper->getNow(),
            'is_quarantine' => $isQuarantine
        ]);

        $message = (new Swift_Message())
            ->setSubject($this->translator->trans($isQuarantine ? 'sick_note.quarantine.title' : 'sick_note.sick.title', [
                '%student%' => $this->converter->convert($note->getStudent()),
                '%grade%' => $note->getStudent()->getGrade()->getName()
            ], 'email'))
            ->setBody($body)
            ->setCc($cc);

        if(!empty($this->settings->getRecipient())) {
            $message->setTo($this->settings->getRecipient());
        }

        if(!empty($note->getEmail())) {
            $message->addBcc($note->getEmail());
            $message->setReplyTo($note->getEmail());
        }

        foreach($note->getAttachments() as $attachment) {
            $message->attach(Swift_Attachment::fromPath($attachment->getRealPath())->setFilename($attachment->getClientOriginalName()));
        }

        $this->mailer->send($message);
    }
}