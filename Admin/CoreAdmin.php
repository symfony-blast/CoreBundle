<?php

namespace Librinfo\CoreBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Mapper\BaseMapper;
use Sonata\AdminBundle\Mapper\BaseGroupedMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Exception\InvalidParameterException;
use Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;
use Sonata\AdminBundle\Admin\Admin as SonataAdmin;
use Librinfo\CoreBundle\Tools\Reflection\ClassAnalyzer;
use Librinfo\CoreBundle\Admin\Traits\CollectionsManager;
use Librinfo\CoreBundle\Admin\Traits\Mapper;

abstract class CoreAdmin extends SonataAdmin
{
    use CollectionsManager,
        Mapper;
    
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $mapper)
    {
        if ( !$this->configureMapper($mapper) )
            $this->fallbackConfiguration($mapper, __FUNCTION__);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $mapper)
    {
        if ( !$this->configureMapper($mapper) )
            $this->fallbackConfiguration($mapper, __FUNCTION__);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $mapper)
    {
        if ( !$this->configureMapper($mapper) )
            $this->fallbackConfiguration($mapper, __FUNCTION__);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $mapper)
    {
        if ( !$this->configureMapper($mapper) )
            $this->fallbackConfiguration($mapper, __FUNCTION__);
    }

    private function fallbackConfiguration(BaseMapper $mapper, $function)
    {
        // fallback
        $rm = new \ReflectionMethod($this->getParentClass(), $function);
        if ( $rm->class == $this->getParentClass() )
            $this->configureFields($function, $mapper, $this->getParentClass());
    }

    /**
     * Returns the level of depth of an array
     * @param  array  $array
     * @param  integer $level : do not use, just used for recursivity
     * @return int : depth
     */
    private static function arrayDepth( $array, $level = 0 )
    {
        if ( !$array )
            return $level;

        if ( !is_array($array) )
            return $level;

        $level++;
        foreach ( $array as $key => $value )
        if ( is_array($value) )
            $level = $level < self::arrayDepth($value, $level) ? self::arrayDepth($value, $level) : $level;

        return $level;
    }
    
    protected function getOriginalClass()
    {
        return get_called_class();
    }
    protected function getParentClasses()
    {
        return class_parents($this->getOriginalClass());
    }
    protected function getParentClass()
    {
        return get_parent_class($this->getOriginalClass());
    }
    protected function getGrandParentClass()
    {
        return get_parent_class(get_parent_class($this->getOriginalClass()));
    }
    protected function configureFields($function, BaseMapper $mapper, $class = NULL)
    {
        if ( !$class )
            $class = $this->getOriginalClass();
        return $class::$function($mapper);
    }

    /**
     * function prePersist
     * @see CoreAdmin::prePersistOrUpdate()
     **/
    public function prePersist($object)
    {
        $this->prePersistOrUpdate($object, 'prePersist');
    }

    /**
     * function prePersist
     * @see CoreAdmin::prePersistOrUpdate()
     **/
    public function preUpdate($object)
    {
        $this->prePersistOrUpdate($object, 'preUpdate');
    }
    
    /**
     * function prePersistOrUpdate
     *
     * Searches in every trait (as if they were kind of Doctrine Behaviors) some logical to be
     * executed during the self::prePersist() or self::preUpdate() calls
     * The logical is stored in the self::prePersist{TraitName}() method
     *
     * @param   Object        $object (Entity)
     * @param   string        $method (the current called method, eg. 'preUpdate' or 'prePersist')
     * @return  CoreAdmin     $this
     **/
    protected function prePersistOrUpdate($object, $method)
    {
        $analyzer = new ClassAnalyzer;
        foreach ( $analyzer->getTraits($this) as $traitname )
        {
            $rc = new \ReflectionClass($traitname);
            if ( method_exists($this, $exec = $method.$rc->getShortName()) )
                $this->$exec($object); // executes $this->prePersistMyTrait() or $this->preUpdateMyTrait() method
        }
        return $this;
    }
}
