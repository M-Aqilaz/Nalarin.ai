<?php

namespace App\Contracts;

use App\Data\AiReplyResult;
use App\Models\ChatThread;

interface AiThreadResponder
{
    public function generateReply(ChatThread $thread): AiReplyResult;
}
