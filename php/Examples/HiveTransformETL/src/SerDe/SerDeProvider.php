<?php
namespace Examples\HiveTransformETL\SerDe;

use \Examples\HiveTransformETL\Application\ApplicationContext,
    \Examples\HiveTransformETL\Application\Conf\HiveTransformerConf,
    \Examples\HiveTransformETL\Application\Conf\HiveTransformerConfVars,
    \Examples\HiveTransformETL\Deserializer\DeserializerProvider,
    \Examples\HiveTransformETL\Serializer\SerializerProvider;

final class SerDeProvider {
    public static function get($className) {
        $conf = ApplicationContext::getInstance()->getConf();
        
        return SerDeFactory::factory($className)
            ->setDeserializer(
                DeserializerProvider::get(
                    $conf->getInputDeserializerClass()        
                )
            )
            ->setSerializer(
                SerializerProvider::get(
                    $conf->getOutputSerializerClass()        
                )
            );
    }
}