<?php
namespace Examples\HiveTransformETL\Deserializer;

use \Examples\HiveTransformETL\Application\ApplicationContext,
    \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf,
    \Examples\HiveTransformETL\Util\ReflectionUtils;

final class DeserializerProvider {
    public static function get($className) {
        $conf = ApplicationContext::getInstance()->getConf();
        $deserializer = DeserializerFactory::factory($className);
        
        switch(ReflectionUtils::resolveClassName($deserializer)) {
            case "Examples\HiveTransformETL\Component\Deserializer\TokenizedDeserializer":
                $deserializer->setToken(
                    $conf->getInputDeserializerToken()
                );
            break;
        }
        
        return $deserializer;
    }
}