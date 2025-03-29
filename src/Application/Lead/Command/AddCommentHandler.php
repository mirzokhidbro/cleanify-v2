<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

use App\Domain\Lead\LeadRepositoryInterface;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepositoryInterface;
use RuntimeException;

class AddCommentHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $leadRepository,
        private readonly CommentRepositoryInterface $commentRepository
    ) {}

    public function handle(AddComment $command): void
    {
        $lead = $this->leadRepository->findById($command->leadId)
            ?? throw new RuntimeException('Lead not found');

        $comment = Comment::create(
            $lead,
            $command->comment,
            'text'
        );

        $this->commentRepository->save($comment);
    }
}
