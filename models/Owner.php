<?php

/**
 * This is the model class for table "house".
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property date $birth_date
 * @property string $email
 * @property string $mobile_number
 * @property string $password
 * @property int $active
 * @property datetime|null $created_at
 * @property datetime|null $updated_at
 */
class Owner extends ActiveRecord implements IdentityInterface
{
    ...
    public function getAllCollection(?array $sortParams): ArrayDataProvider
    {
        $attributesHelper = new AttributesHelper();

        $query = self::find()->select(['id', 'first_name', 'last_name', 'middle_name', 'birth_date',
            'email', 'mobile_number', 'token', 'active', 'created_at', 'updated_at', 'user_id'])
            ->with([
                'user' => function ($query) {
                    $query->select(['id', 'username', 'email', 'active']);
                },
                'apartments' => function ($query) {
                    $query->with([
                        'apartment' => function ($query) {
                            $query->select(['id', 'house_id', 'number', 'size', 'floor', 'active'])
                                ->with([
                                    'house' => function ($query) {
                                        $query->select(['id', 'user_id', 'name', 'address', 'floor', 'logo', 'active', 'created_at', 'updated_at'])
                                            ->with([
                                                'user' => function ($query) {
                                                    $query->select(['id', 'username', 'email', 'active']);
                                                }
                                            ]);
                                    }
                                ]);
                        }
                    ]);
                }
            ])
            ->orderBy($sortParams ?? ['created_at' => SORT_ASC])
            ->asArray()
            ->all();

        $data = $attributesHelper->replaceAttributes($query, [
            'logo' => [
                'params' => [
                    'folderName' => $this->folderName,
                ],
                'methodName' => 'getFullAssetPath'
            ]
        ]);

        return (new ArrayDataProvider([
            'allModels' => $data,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]));

    }

    ...

}
