<?php


namespace ZW\Laravel\Eloquent\Custom;

use Illuminate\Database\Eloquent\SoftDeletes as ESoftDeletes;

trait SoftDeletes
{
    use ESoftDeletes;


    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope());
    }


    /**
     * Initialize the soft deleting trait for an instance.
     *
     * @return void
     */
    public function initializeSoftDeletes()
    {
    }



    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();
        $value = $this->getDeletedValue();

        $columns = [$this->getDeletedColumn() => $value];

        $this->{$this->getDeletedColumn()} = $value;

        if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }


    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedColumn()} = $this->getUnDeletedValue();

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }



    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed()
    {
        return $this->{$this->getDeletedColumn()} == $this->getDeletedValue();
    }



    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        return defined('static::DELETED_AT') ? static::DELETED_AT : 'is_deleted';
    }

    /**
     * Get the name of the "deleted" column.
     *
     * @return string
     */
    public function getDeletedColumn()
    {
        return defined('static::DELETED') ? static::DELETED : 'is_deleted';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedColumn()
    {
        return $this->qualifyColumn($this->getDeletedColumn());
    }

    /**
     * 获取删除值
     *
     * @return string
     */
    public function getDeletedValue()
    {
        return defined('static::DELETED_VALUE') ? static::DELETED_VALUE : 1;
    }

    /**
     * 获取未删除值
     *
     * @return string
     */
    public function getUnDeletedValue()
    {
        return defined('static::UN_DELETED_VALUE') ? static::UN_DELETED_VALUE : 0;
    }

}
