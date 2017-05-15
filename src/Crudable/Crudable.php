<?php

namespace Flobbos\Crudable;

trait Crudable {
    
    protected $relation = [];
    protected $withHasMany,$withBelongsToMany;
    /**
     * Get a single item or collection
     * @param int $id
     * @return Model/Collection
     */
    public function get($id = null){
        if(!is_null($id)){
            return $this->find($id);
        }
        return $this->model->with($this->relation)->get();
    }
    
    /**
     * Get paginated collection
     * @param int $perPage
     * @return Collection
     */
    public function paginate($perPage){
        return $this->model->with($this->relation)->paginate($perPage);
    }
    
    /**
     * Alias of model find
     * @param int $id
     * @return Model
     */
    public function find($id){
        return $this->model->with($this->relation)->find($id);
    }
    
    /**
     * Retrieve single trashed item or all
     * @param int $id
     * @return Model/Collection
     */
    public function getTrash($id = null){
        if(!is_null($id)){
            return $this->getTrashedItem($id);
        }
        return $this->model->onlyTrashed()->with($this->relation)->get();
    }
    
    /**
     * Return single trashed item
     * @param int $id
     * @return Model
     */
    public function getTrashedItem($id){
        return $this->model->withTrashed()->with($this->relation)->find($id);
    }
    
    /**
     * Set relationship for retrieving model and relations
     * @param array $relation
     * @return self
     */
    public function setRelation(array $relation){
        $this->relation = $relation;
        return $this;
    }
    
    /**
     * Create new database entry including related models
     * @param array $data
     * @param string $relationName
     * @return Model
     */
    public function create(array $data, $relationName = null){
        $model = $this->model->create($data);
        //check for hasMany
        if(!is_null($this->withHasMany) && !is_null($relationName)){
            $model->{$relationName}()->saveMany($this->withHasMany);
        }
        //check for belongsToMany
        if(!is_null($this->withBelongsToMany) && !is_null($relationName)){
            $model->{$relationName}()->sync($this->withBelongsToMany);
        }
        return $model;
    }
    
    /**
     * Update Model
     * @param array $data
     * @return bool
     */
    public function udpate($id, array $data){
        $model = $this->find($id);
        return $model->update($data);
    }
    
    /**
     * Delete model either soft or hard delete
     * @param int $id
     * @param bool $hardDelete
     * @return bool
     */
    public function delete($id, $hardDelete = false){
        $model = $this->model->find($id);
        if($hardDelete){
            return $model->forceDelete($id);
        }
        return $model->delete($id);
    }
    
    /**
     * Set related models that need to be created
     * for a hasMany relationship
     * @param array $data
     * @param string $relatedModel
     * @return self
     */
    public function withHasMany(array $data, $relatedModel){
        $this->withHasMany = [];
        foreach($data as $k=>$v){
            $this->withHasMany[] = new $relatedModel($v);
        }
        return $this;
    }
    
    /**
     * Set related models for belongsToMany relationship
     * @param array $data
     * @return self
     */
    public function withBelongsToMany(array $data){
        $this->withBelongsToMany = $data;
        return $this;
    }
    
}