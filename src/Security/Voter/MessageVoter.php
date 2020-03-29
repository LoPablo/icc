<?php

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Message\MessageConfirmationHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter {

    const New = 'new-message';
    const View = 'view';
    const Edit = 'edit';
    const Remove = 'remove';
    const Confirm = 'confirm';
    const Dismiss = 'dismiss';
    const Download = 'download';
    const Upload = 'upload';

    private $accessDecisionManager;
    private $confirmationHelper;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, MessageConfirmationHelper $confirmationHelper) {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->confirmationHelper = $confirmationHelper;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::View,
            static::Edit,
            static::Remove,
            static::Confirm,
            static::Dismiss,
            static::Download,
            static::Upload
        ];

        return $attribute === static::New || (in_array($attribute, $attributes) && $subject instanceof Message);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::View:
                return $this->canView($subject, $token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);

            case static::Confirm:
                return $this->canConfirm($subject, $token);

            case static::Dismiss:
                return $this->canDismiss($subject, $token);

            case static::Download:
                return $this->canDownload($subject, $token);

            case static::Upload:
                return $this->canUpload($subject, $token);
        }

        throw new \LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_CREATOR']);
    }

    private function canView(Message $message, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        // Admins see all messages
        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_ADMIN']) || $this->accessDecisionManager->decide($token, ['ROLE_KIOSK'])) {
            return true;
        }

        // Teachers can see all messages
        if($user->getUserType()->equals(UserType::Teacher())) {
            return true;
        }

        // You can see your own messages
        if($message->getCreatedBy()->getId() === $user->getId()) {
            return true;
        }

        if($this->checkMessageIsForVisibility($message, $user->getUserType(), false) !== true) {
            return false;
        }

        if($user->getUserType()->equals(UserType::Student()) !== true && $user->getUserType()->equals(UserType::Parent()) !== true) {
            // all checks passed for non-student/-parent users
            return true;
        }

        /** @var Student[] $students */
        $students = $user->getStudents();
        $messageStudyGroupIds = $message->getStudyGroups()->map(function(StudyGroup $group) {
            return $group->getId();
        })->toArray();

        foreach($students as $student) {
            $studyGroupIds = $student->getStudyGroupMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudyGroup()->getId();
            })->toArray();

            if(count(array_intersect($messageStudyGroupIds, $studyGroupIds)) > 1) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_CREATOR']) !== true) {
            return false;
        }

        if($this->accessDecisionManager->decide($token, ['ROLE_MESSAGE_ADMIN'])) {
            // Admins can edit all messages
            return true;
        }

        // Creators can only edit their messages
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $message->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(Message $message, TokenInterface $token) {
        return $this->canEdit($message, $token);
    }

    private function canConfirm(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return false;
        }

        return $message->mustConfirm() && $this->isMessageForUser($message, $token);
    }

    private function canDismiss(Message $message, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return false;
        }

        if($message->mustConfirm() === false || $this->canConfirm($message, $token) === false) {
            return true;
        }

        // only allow dismissing message in case the user has confirmed the message!
        return $this->confirmationHelper->isMessageConfirmed($message, $token->getUser());
    }

    private function checkMessageIsForVisibility(Message $message, UserType $type, bool $strict = true): bool {
        // Only targeted users are allowed to confirm
        $visibilities = $message->getVisibilities()
            ->map(function(UserTypeEntity $messageVisibility) {
                return $messageVisibility->getUserType()->getKey();
            })
            ->toArray();

        /** @var UserType[] $present */
        $present = [ $type ];

        if($strict === false && $type->equals(UserType::Parent())) {
            $present[] = UserType::Student();
        }

        foreach($present as $userType) {
            if(in_array($userType->getKey(), $visibilities) === true) {
                return true;
            }
        }

        return false;
    }

    private function isMessageForUser(Message $message, TokenInterface $token) {
        if($this->canView($message, $token) !== true) {
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($this->checkMessageIsForVisibility($message, $user->getUserType()) === true) {
            return true;
        }

        return false;
    }

    private function canDownload(Message $message, TokenInterface $token) {
        return $message->isDownloadsEnabled() && $this->isMessageForUser($message, $token);
    }

    private function canUpload(Message $message, TokenInterface $token) {
        return $message->isUploadsEnabled() && $this->isMessageForUser($message, $token);
    }
}