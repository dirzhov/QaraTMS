<?php

namespace App\Traits;

trait HasModelEventHooks
{
    /**
     * List of Eloquent events and their associated methods.
     *
     * The trait supports all standard Eloquent events:
     *
     * retrieved: After a model is retrieved from the database
     * creating: Before a new model is created
     * created: After a new model is created
     * updating: Before an existing model is updated
     * updated: After an existing model is updated
     * saving: Before saving (creating or updating)
     * saved: After saving (creating or updating)
     * deleting: Before a model is deleted
     * deleted: After a model is deleted
     * trashed: After a model is soft deleted
     * forceDeleting: Before a model is force deleted
     * forceDeleted: After a model is force deleted
     * restoring: Before a soft deleted model is restored
     * restored: After a soft deleted model is restored
     * replicating: When a model is replicated
 */
    protected static $modelEvents = [
        'retrieved',
        'creating',
        'created',
        'updating',
        'updated',
        'saving',
        'saved',
        'deleting',
        'deleted',
        'trashed',
        'forceDeleting',
        'forceDeleted',
        'restoring',
        'restored',
        'replicating',
    ];

    /**
     * Default prefix for event methods.
     */
    protected static $defaultEventMethodPrefix = 'on';

    /**
     * Boot the trait and attach the model event hooks with a dynamic method prefix.
     */
    protected static function bootHasModelEventHooks()
    {
        foreach (static::$modelEvents as $event) {

            if (method_exists(static::class, $event)) {
                static::$event(function ($model) use ($event) {
                    $prefix = $model->getEventMethodPrefix();
                    $method = $prefix . ucfirst($event);

                    // Si la méthode existe dans le modèle, elle est appelée
                    if (method_exists($model, $method)) {
                        $model->{$method}($model);
                    }
                });
            }
        }
    }

    /**
     * Retrieve the prefix of event methods.
     *
     * @return string
     */
    public function getEventMethodPrefix()
    {
        // Si le modèle a défini un préfixe, on l'utilise, sinon on prend le préfixe par défaut
        return property_exists($this, 'eventMethodPrefix') ? $this->eventMethodPrefix : static::$defaultEventMethodPrefix;
    }
}