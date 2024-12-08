<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActivitiesModel extends Model
{
    protected $table = 'user_activities';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'activity_id', 'score', 'is_done', 'sequence', 'requires_help'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function markAsDoneAnActivity($userId, $activityId)
    {
        // Check if the activity already exists for the user
        $existingActivity = $this->where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->first();

        if ($existingActivity) {
            // If it exists, update it
            $maxSequence = $this->where('user_id', $userId)
                ->where('is_done', 1)
                ->selectMax('sequence')
                ->first()['sequence'] ?? 0;

            return $this->updateActivity($userId, $activityId, [
                'is_done' => 1,
                'sequence' => $maxSequence + 1,
            ]);
        } else {
            // If it does not exist, insert a new record
            $maxSequence = $this->where('user_id', $userId)
                ->where('is_done', 1)
                ->selectMax('sequence')
                ->first()['sequence'] ?? 0;

            return $this->insert([
                'user_id' => $userId,
                'activity_id' => $activityId,
                'is_done' => 1,
                'sequence' => $maxSequence + 1,
            ]);
        }
    }

    public function unMarkAsDoneAnActivity($userId, $activityId)
    {
        // Fetch the current sequence of the activity to be unmarked
        $activity = $this->where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->first();

        if (!$activity) {
            return false; // Activity not found
        }

        $currentSequence = $activity['sequence'];

        // Update the activity to unmark as done
        $this->updateActivity($userId, $activityId, [
            'is_done' => 0,
            'sequence' => null,
        ]);

        // Rearrange sequence for remaining activities
        $this->rearrangeSequence($userId, $currentSequence);

        return true;
    }

    private function updateActivity($userId, $activityId, $data)
    {
        return $this->where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->set($data)
            ->update();
    }

    private function rearrangeSequence($userId, $startingSequence)
    {
        // Fetch activities with sequence greater than the starting sequence
        $activities = $this->where('user_id', $userId)
            ->where('sequence >', $startingSequence)
            ->where('is_done', 1)
            ->orderBy('sequence', 'ASC')
            ->findAll();

        // Update sequence numbers
        foreach ($activities as $index => $activity) {
            $this->updateActivity($userId, $activity['activity_id'], [
                'sequence' => $startingSequence + $index,
            ]);
        }
    }
    public function getUserActivities($userId)
    {
        // Fetch the activities for the given user
        return $this->where('user_id', $userId)
            ->findAll();
    }

}
