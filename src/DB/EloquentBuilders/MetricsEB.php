<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentBuilders;

use Delta4op\Laravel\Tracker\DB\Models\Entry;

class MetricsEB extends EloquentBuilder
{
    /**
     * @param int $id
     * @return MetricsEB
     */
    public function whereId(int $id): MetricsEB
    {
        return $this->where('id', $id);
    }

    /**
     * @param string $uuid
     * @return MetricsEB
     */
    public function whereUuid(string $uuid): MetricsEB
    {
        return $this->where('uuid', $uuid);
    }

    /**
     * @param string $familyHash
     * @return MetricsEB
     */
    public function whereFamilyHash(string $familyHash): MetricsEB
    {
        return $this->where('family_hash', $familyHash);
    }

    /**
     * @param Entry|int $entryId
     * @return MetricsEB
     */
    public function whereEntryId(Entry|int $entryId): MetricsEB
    {
        $entryId = $entryId instanceof Entry ? $entryId->id : $entryId;

        return $this->where('entry_id', $entryId);
    }

    /**
     * @param string $batchId
     * @return MetricsEB
     */
    public function whereBatchId(string $batchId): MetricsEB
    {
        return $this->where('batch_id', $batchId);
    }

    /**
     * @param string $id
     * @return MetricsEB
     */
    public function whereEnvId(string $id): MetricsEB
    {
        return $this->where('env_id', $id);
    }

    /**
     * @param string $id
     * @return MetricsEB
     */
    public function whereSourceId(string $id): MetricsEB
    {
        return $this->where('source_id', $id);
    }
}
