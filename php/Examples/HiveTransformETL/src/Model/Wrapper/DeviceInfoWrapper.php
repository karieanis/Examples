<?php
namespace Examples\HiveTransformETL\Model\Wrapper;

/**
 * Wrapper class containing generalised business logic which can be applied to device info models
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class DeviceInfoWrapper {
    /**
     * A shared flyweight object
     * @var \Examples\HiveTransformETL\Model\Wrapper\DeviceInfoWrapper
     */
    protected static $flyweight;
    
    /**
     * 
     * @var \Examples\HiveTransformETL\Model\DeviceInfo
     */
    protected $innerModel;
    
    /**
     * 
     * @var boolean
     */
    protected $unknown;
    
    /**
     * Constructor
     * @param \Examples\HiveTransformETL\Model\DeviceInfo $info
     */
    public function __construct(\Examples\HiveTransformETL\Model\DeviceInfo $info) {
        $this->setInnerModel($info)
            ->init();
    }
    
    /**
     * Initialise this wrapper class
     */
    protected function init() {
        $model = $this->getInnerModel();
        
        // business logic which dictates if a model represents an unknown UA
        $this->unknown = is_null($model->getMobileVendor()) &&
            is_null($model->getMobileModel()) &&
            "Other" === $model->getBrowserName() &&
            "Other" === $model->getOsName() &&
            "" === $model->getBrowserVersion() &&
            "" === $model->getOsVersion();
    }

    /**
     * Check if the wrapped device info object represents an unknown user agent
     * @return boolean
     */
    public function isUnknown() {
        return $this->unknown;
    }
    
    /**
     * Get the wrapped device info object
     * @return \Examples\HiveTransformETL\Model\DeviceInfo
     */
    protected function getInnerModel() {
        return $this->innerModel;
    }
    
    /**
     * Set the wrapped device info object
     * @param \Examples\HiveTransformETL\Model\DeviceInfo $info
     * @return \Examples\HiveTransformETL\Model\Wrapper\DeviceInfoWrapper
     */
    protected function setInnerModel(\Examples\HiveTransformETL\Model\DeviceInfo $info) {
        $this->innerModel = $info;
        return $this;
    }
    
    /**
     * Wrap the passed info object in an instance of this class
     * @static
     * @param \Examples\HiveTransformETL\Model\DeviceInfo $info
     * @return \Examples\HiveTransformETL\Model\Wrapper\DeviceInfoWrapper
     */
    public static function wrap(\Examples\HiveTransformETL\Model\DeviceInfo $info) {
        return new static($info);
    }
    
    /**
     * Utilise the flyweight object to wrap the passed device info object. This cuts down on the amount of 
     * construct instructions required for the application.
     * 
     * @static
     * @param \Examples\HiveTransformETL\Model\DeviceInfo $info
     * @return \Examples\HiveTransformETL\Model\Wrapper\DeviceInfoWrapper
     */
    public static function fly(\Examples\HiveTransformETL\Model\DeviceInfo $info) {
        if(is_null(self::$flyweight)) {
            self::$flyweight = self::wrap($info);
        } else if($info !== self::$flyweight->getInnerModel()) {
            self::$flyweight->setInnerModel($info)->init();
        }
        
        return self::$flyweight;
    }
}