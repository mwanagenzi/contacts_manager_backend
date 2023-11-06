<?php

namespace App\Transformers;

use App\Models\Contact;
use App\Models\Group;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class GroupTransformer extends TransformerAbstract
{
    public function transform(Group $group)
    {
        $contacts = Contact::query()->where('group_id', '=', $group->id)->get();

        return [
            'id' => $group->id,
            'group_name' => $group->name,
            'contacts' => fractal($contacts, ContactTransformer::class, ArraySerializer::class)
        ];
    }
}
