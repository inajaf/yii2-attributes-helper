<?php

class AttributesHelper
{
    public function replaceAttributes(array $data, array $attributes): array
    {
        $params = [];
        $value = '';

        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = $this->replaceAttributes($val, $attributes);
            }

            foreach ($attributes as $attribute => $values) {
                if (is_array($values)) {
                    foreach ($values as $attributeKey => $attributeValue) {
                        $params[$attributeKey] = $attributeValue;
                    }
                } else {
                    $params = [];
                    $value = $values;
                }

                if ($key === $attribute) {
                    if (!empty($params['params'])) {
                        $params['value'] = $val;
                        $data[$key] = $this->{$params['methodName']}($params);
                    } else {
                        $data[$key] = $value;
                    }
                }
            }

        }

        return $data;
    }

    private function getFullAssetPath(array $params): ?string
    {
        return (new AssetHelper())->getAssetPath($params['value'], $params['params']['folderName']);
    }

    ...
}
