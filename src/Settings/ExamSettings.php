<?php

namespace App\Settings;

use App\Entity\Grade;
use App\Entity\UserType;

class ExamSettings extends AbstractSettings {

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getVisibility() {
        return $this->getValue('exams.visibility', [ ]);
    }

    public function setVisibility(array $visibility) {
        $this->setValue('exams.visibility', $visibility);
    }

    public function isVisibileFor(UserType $type) {
        foreach($this->getVisibility() as $visibleUserType) {
            if($type === $visibleUserType) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the number of days students can view in the future. Detaults to 0 (unlimited days)
     */
    public function getTimeWindowForStudents(): int {
        return (int)$this->getValue('exams.window', 0);
    }

    public function setTimeWindowForStudents(int $timeWindow) {
        $this->setValue('exams.window', $timeWindow);
    }

    /**
     * Returns the number of days students can view supervisions for future exams. Defaults to 0 (unlimited days)
     */
    public function getTimeWindowForStudentsToSeeSupervisions(): int {
        return (int)$this->getValue('exams.window.supervisions', 0);
    }

    public function setTimeWindowForStudentsToSeeSupervisions(int $timeWindow) {
        $this->setValue('exams.window.supervisions', $timeWindow);
    }

    public function isNotificationsEnabled(): bool {
        return (bool)$this->getValue('exams.notifications.enabled', false);
    }

    public function setNotificationsEnabled(bool $enabled): void {
        $this->setValue('exams.notifications.enabled', $enabled);
    }

    public function getNotificationReplyToAddress(): ?string {
        return (string)$this->getValue('exams.notifications.reply_to', null);
    }

    public function setNotificationReplyToAddress(?string $address): void {
        $this->setValue('exams.notifications.reply_to', $address);
    }

    public function getNotificationSender(): ?string {
        return (string)$this->getValue('exams.notifications.sender', null);
    }

    public function setNotificationSender(?string $sender): void {
        $this->setValue('exams.notifications.sender', $sender);
    }

    public function getMaximumNumberOfExamsPerWeek(Grade $grade): int {
        return (int)$this->getValue(sprintf('exams.max_per_week.%d', $grade->getId()), 2);
    }

    public function setMaximumNumberOfExamsPerWeek(Grade $grade, int $number): void {
        $this->setValue(sprintf('exams.max_per_week.%d', $grade->getId()), $number);
    }

    public function getMaximumNumberOfExamsPerDay(): int {
        return (int)$this->getValue('exams.max_per_day', 1);
    }

    public function setMaximumNumberOfExamsPerDay(int $number): void {
        $this->setValue('exams.max_per_day', $number);
    }

    /**
     * @return int[]
     */
    public function getVisibleGradeIds(): array {
        return $this->getValue('exams.visible_for', [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setVisibleGradeIds(array $ids): void {
        $this->setValue('exams.visible_for', $ids);
    }

    public function isRoomReservationAllowed(): bool {
        return $this->getValue('exams.reservation_enabled', true);
    }

    public function setIsRoomReservationAllowed(bool $isAllowed): void {
        $this->setValue('exams.reservation_enabled', $isAllowed);
    }
}