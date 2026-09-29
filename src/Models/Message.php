<?php
/**
 * This file handle users messages operations and validation
*/
namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use Exception;

class Message {
    
    private const TABLE = "messages";
    private ?int $message_id = null;
    private string $content = "";
    private string $sent_at = "";
    private bool $is_sent = TRUE;
    private bool $is_delivered = FALSE;
    private bool $is_read = FALSE;
    private ?int $discussion_id = null;
    private ?int $sender_id = null;
    private ?int $receiver_id = null;

    /**
     * Build a message object from a database row
     * 
     * @param array $row Data returned by the message table
     * @return self A message instance filled with table values
     */
    public static function fromArray(array $row): Message
    {
        $message = new self();
        $message->message_id = (int)$row['message_id'] ?? null;
        $message->content = $row['content'] ?? "";
        $message->sent_at = $row['sent_at'] ?? "";
        $message->is_sent = (bool)$row['is_sent'] ?? TRUE;
        $message->is_delivered = (bool)$row['is_delivered'] ?? FALSE;
        $message->is_read = (bool)$row['is_read'] ?? FALSE;
        $message->discussion_id = (int)$row['discussion_id'] ?? null;
        $message->sender_id = (int)$row['sender_id'] ?? null;
        $message->receiver_id = (int)$row['receiver_id'] ?? null;

        return $message;
    }

    /**
     * Get a message by its ID
     * 
     * @param int $message_id Message identifier
     * @return self|null The message instance if it exist, otherwise null
     */
    public static function findById(int $message_id): ?self
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE message_id = :id";
        $params = ["id" => $message_id];

        $result = Database::fetch($sql, $params);

        return $result ? self::fromArray($result) : null;
    }

    /**
     * Get all messages by sending date
     * 
     * @param string $sent_at The date which the message is sent
     * @return array List of messages found
     */
    public static function findByDate(string $sent_at): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE sent_at LIKE :date";
        $params = [":date" => "%{$sent_at}%"];
        $results = Database::fetchAll($sql, $params);

        $messages = [];
        foreach ($results as $result) {
            $messages[] = self::fromArray($result);
        }
        
        return $messages;
    }

    /**
     * Get all the messages sent in a specific chat
     * 
     * @param int $discussion_id The discussion identifier
     * @return array List of messages found
     */
    public static function findByDiscussion(int $discussion_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE discussion_id = :id";
        $params = ["id" => $discussion_id];
        $results = Database::fetchAll($sql, $params);

        $messages = [];
        foreach ($results as $result) {
            $messages[] = self::fromArray($result);
        }
        
        return $messages;
    }

    /**
     * Get all the messages sent by a specific sender
     * 
     * @param int $sender_id The sender (customer/seller) identifier
     * @return array List of messages found
     */
    public static function findBySender(int $sender_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE sender_id = :id";
        $params = ["id" => $sender_id];
        $results = Database::fetchAll($sql, $params);

        $messages = [];
        foreach ($results as $result) {
            $messages[] = self::fromArray($result);
        }
        
        return $messages;
    }

    /**
     * Get all the messages sent by a specific recipient
     * 
     * @param int $receiver_id The receiver (seller/customer) identifier
     * @return array List of messages found
     */
    public static function findByReceiver(int $receiver_id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE receiver_id = :id";
        $params = ["id" => $receiver_id];
        $results = Database::fetchAll($sql, $params);

        $messages = [];
        foreach ($results as $result) {
            $messages[] = self::fromArray($result);
        }
        
        return $messages;
    }

    /**
     * Retrieve all sent messages
     * 
     * @return array List of all messages sent
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY sent_at";
        $results = Database::fetchAll($sql);

        $messages = [];
        foreach ($results as $result) {
            $messages[] = self::fromArray($result);
        }
        
        return $messages;
    }

    /**
     * Create a message in the database
     * 
     * @throws Exception When message ID is missing
     * @return bool True on success
     */
    public function create(): bool
    {
        $validator = new Validator();
        $validator->validMessage("content", "is_sent", "is_read", "discussionId", "senderId", "receiverId", [
            "content" => $this->content,
            "is_sent" => $this->is_sent,
            "is_read" => $this->is_read,
            "discussionId" => $this->discussion_id,
            "senderId" => $this->sender_id,
            "receiverId" => $this->receiver_id
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode("; ", $validator->getErrors()));
        }
        
        $this->sent_at = date("Y-m-d H:i:s");
        $this->is_sent = TRUE;
        $this->is_delivered = FALSE;

        $sql = "INSERT INTO " . self::TABLE . " 
        (content, sent_at, is_sent, is_delivered, is_read, discussion_id, sender_id, receiver_id) 
        VALUES (:content, :sent_at, :is_sent, :is_delivered, :is_read, :discussion_id, :sender_id, :receiver_id)";
        $params = [
            "content" => $this->content,
            "sent_at" => $this->sent_at,
            "is_sent" => $this->is_sent ?? TRUE,
            "is_delivered" => $this->is_delivered,
            "is_read" => $this->is_read ?? FALSE,
            "discussion_id" => $this->discussion_id,
            "sender_id" => $this->sender_id,
            "receiver_id" => $this->receiver_id
        ];

        Database::query($sql, $params);

        $this->message_id = (int)Database::lastInsertId();
        return true;
    }

    /**
     * Update a message in the database
     * 
     * @throws Exception When message ID is missing or when other fields are incorrectly set
     * @return bool True on success
     */
    public function update(): bool
    {
        $validator = new Validator();
        $validator->checkMessage("message id", $this->message_id)
            ->validMessageUpdate("content", "is_read",[
            "content" => $this->content,
            "is_read" => $this->is_read
        ]);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode("; ", $validator->getErrors()));
        }

        if ($this->is_read && !$this->is_delivered) {
            throw new Exception("A message not delivered can't be marked as is_read");
        }

        $sql = "UPDATE " . self::TABLE . " SET 
            content = :content,
            is_read = :is_read,
            is_delivered = :is_delivered
            WHERE message_id = :id
        ";
        $params = [
            "id" => $this->message_id,
            "content" => $this->content,
            "is_read" => $this->is_read,
            "is_delivered" => $this->is_delivered
        ];

        Database::query($sql, $params);

        return true;
    }

    /**
     * Mark a message as delivered when it gets to the receiver
     * 
     * @throws Exception Fail when the message is not sent first
     * @return bool The delivery state
     */
    public function markAsDelivered(): bool
    {
        if (!$this->is_sent) {
            throw new Exception("A message not sent can't be marked as delivered");
        }

        $sql = "UPDATE " . self::TABLE . " SET 
            is_delivered = :is_delivered
            WHERE message_id = :id
        ";
        Database::query($sql,[
            "id" => $this->message_id,
            "is_delivered" => TRUE
        ]);

        $this->is_delivered = TRUE;

        return true;
    }

    /**
     * Mark a message as read when it gets to the receiver
     * 
     * @throws Exception Fail when the message is not delivered
     * @return bool The read state
     */
    public function markAsRead(): bool
    {
        if (!$this->is_delivered) {
            throw new Exception("A message not delivered can't be marked as read");
        }

        $sql = "UPDATE " . self::TABLE . " SET 
            is_read = :is_read
            WHERE message_id = :id
        ";
        Database::query($sql, [
            "id" => $this->message_id,
            "is_read" => TRUE
        ]);
        
        $this->is_read = TRUE;

        return true;
    }

    /**
     * Delete a message in the database
     * 
     * @throws Exception When the message ID is missing
     * @return bool True success
     */
    public function delete(): bool
    {
        $validator = new Validator();
        $validator->checkMessage("message id", $this->message_id);

        if (!$validator->isValid()) {
            throw new Exception("VALIDATION ERROR : " . implode("; ", $validator->getErrors()));
        }

        $sql = "DELETE FROM " . self::TABLE . " WHERE message_id = :id";
        $params = [
            "id" => $this->message_id
        ];
        
        $statement = Database::query($sql, $params);

        if ($statement->rowCount() === 0) {
            throw new Exception("DELETION FAIL : The message doesn't exist");
        }

        return true;
    }

    /**
     * Set an ID for the message
     * 
     * @param int $message_id
     * @return self The current message
     */
    public function setMessageId(int $message_id): self
    {
        $this->message_id = $message_id;
        return $this;
    }

    /**
     * Set the message content
     * 
     * @param string $content The message content
     * @return self The current message
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the message read state to confirm the receiver has read the message
     * 
     * @param bool $is_read Check if message has been read or not
     * @return self The current message
     */
    public function setReadState(bool $is_read): self
    {
        $this->is_read = $is_read;
        return $this;
    }

    /**
     * Set the message discussion ID
     * 
     * @param int $discussion_id The discussion contaning the message
     * @return self The current message
     */
    public function setDiscussionId(int $discussion_id): self
    {
        $this->discussion_id = $discussion_id;
        return $this;
    }

    /**
     * Set the message sender ID
     * 
     * @param int $sender_id The user who sent the message
     * @return self The current message
     */
    public function setSender(int $sender_id): self
    {
        $this->sender_id = $sender_id;
        return $this;
    }

    /**
     * Set the message receiver ID
     * 
     * @param int $receiver_id The user who receive the message
     * @return self The current message
     */
    public function setReceiver(int $receiver_id): self
    {
        $this->receiver_id = $receiver_id;
        return $this;
    }

    /**
     * Get the message ID
     * 
     * @return ?int The message ID from the object
     */
    public function getMessageId(): ?int { return $this->message_id; }

    /**
     * Get the messaage content
     * 
     * @return string The message content from the object
     */
    public function getContent(): string { return $this->content; }

    /**
     * Get the message sending date
     * 
     * @return string The date on which the message has been sent
     */
    public function getSendDate(): string { return $this->sent_at; }

    /**
     * Get the message send state
     * 
     * @return bool Confirm the message is sent
     */
    public function getSendState(): bool { return $this->is_sent; }
    
    /**
     * Get the message delivery state
     * 
     * @return bool
     */
    public function getDeliveredState(): bool { return $this->is_delivered; }

    /**
     * Get the message reading state
     * 
     * @return bool Confirm that the message has been read
     */
    public function getReadState(): bool { return $this->is_read; }

    /**
     * Get the message discussion ID
     * 
     * @return ?int The discussion in which the message is sent
     */
    public function getDiscussionId(): ?int { return $this->discussion_id; }

    /**
     * Get the message sender ID
     * 
     * @return ?int The user who sent the message
     */
    public function getSender(): ?int { return $this->sender_id; }

    /**
     * Get the message receiver ID
     * 
     * @return ?int The user who receiver the message
     */
    public function getReceiver(): ?int { return $this->receiver_id; }
    
}