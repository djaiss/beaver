<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What a support conversation is about, chosen by the user when they open it.
 *
 * The category only steers the conversation: it sets the tone of the intro the
 * user reads before writing, and it labels the conversation in the list. It does
 * not change who reads the message or what happens to it afterwards.
 */
enum SupportCategory: string
{
    case FeatureRequest = 'feature_request';
    case Billing = 'billing';
    case EmailDelivery = 'email_delivery';
    case HowTo = 'how_to';
    case BugReport = 'bug_report';

    public function label(): string
    {
        return match ($this) {
            self::FeatureRequest => __('Feature request'),
            self::Billing => __('Billing'),
            self::EmailDelivery => __('Email delivery'),
            self::HowTo => __('How-to'),
            self::BugReport => __('Bug report'),
        };
    }

    /**
     * The prompt shown on the picker card, phrased from the user's point of view.
     */
    public function prompt(): string
    {
        return match ($this) {
            self::FeatureRequest => __('I want to request a feature'),
            self::Billing => __('I have a billing question'),
            self::EmailDelivery => __('I\'m not receiving emails'),
            self::HowTo => __('I\'m confused about how something works'),
            self::BugReport => __('I think something is broken'),
        };
    }

    /**
     * The lucide icon name used on the picker card.
     */
    public function icon(): string
    {
        return match ($this) {
            self::FeatureRequest => 'sparkles',
            self::Billing => 'credit-card',
            self::EmailDelivery => 'mail',
            self::HowTo => 'circle-help',
            self::BugReport => 'bug',
        };
    }

    /**
     * The heading shown once the category is picked, above the message field.
     */
    public function heading(): string
    {
        return match ($this) {
            self::FeatureRequest => __('I\'d love to hear your ideas.'),
            self::Billing => __('Let\'s sort it out.'),
            self::EmailDelivery => __('Let\'s figure this out.'),
            self::HowTo => __('Thank you for telling me.'),
            self::BugReport => __('Let\'s get it fixed.'),
        };
    }

    /**
     * The reassuring paragraphs shown under the heading.
     *
     * @return list<string>
     */
    public function paragraphs(): array
    {
        return match ($this) {
            self::FeatureRequest => [
                __('This app is built one improvement at a time, and many of its features started as suggestions from users.'),
                __('Tell me what you\'d like to see, but also tell me why it matters to you. Understanding the problem is often more valuable than the solution itself.'),
                __('I can\'t promise every idea will make it into the app, but I promise I\'ll read every single one.'),
            ],
            self::Billing => [
                __('If you have a question about your subscription, a payment, a renewal, or anything related to billing, send me the details.'),
                __('I\'ll investigate and get back to you as quickly as I can.'),
            ],
            self::EmailDelivery => [
                __('If you\'re expecting an email but nothing has arrived, let me know what you were trying to do and which email address you\'re using.'),
                __('It\'s also worth checking your spam or junk folder, just in case.'),
                __('If the problem is on my side, I\'ll fix it.'),
            ],
            self::HowTo => [
                __('If something feels confusing, that\'s valuable feedback.'),
                __('Tell me what you were trying to accomplish and where things stopped making sense.'),
                __('I\'ll explain how it works, and if I can make the experience clearer for everyone, I\'ll improve it.'),
            ],
            self::BugReport => [
                __('If you\'ve found a bug, tell me exactly what happened, what you expected to happen instead, and what device you\'re using.'),
                __('If you can include a screenshot or screen recording, that\'s incredibly helpful.'),
                __('Thank you for taking the time to report it. Every bug report makes the app a little better.'),
            ],
        };
    }
}
