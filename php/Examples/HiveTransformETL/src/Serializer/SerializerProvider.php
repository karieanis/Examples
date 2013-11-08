<?php
namespace Examples\HiveTransformETL\Serializer;

use \Examples\HiveTransformETL\Application\ApplicationContext,
    \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf,
    \Examples\HiveTransformETL\Util\ReflectionUtils;

final class SerializerProvider {
    public static function get($className) {
        $conf = ApplicationContext::getInstance()->getConf();
        $serializer = SerializerFactory::factory($className);
        
        switch(ReflectionUtils::resolveClassName($serializer)) {
            case "Examples\HiveTransformETL\Component\Serializer\TokenizedSerializer":
                $serializer->setToken(
                    $conf->getOutputSerializerToken()
                );
            break;
        }
        
        return $serializer;
    }
}