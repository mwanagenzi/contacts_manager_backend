<?php

namespace App\Transformers;

use App\Models\Contact;
use App\Models\Group;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    public function transform(Contact $contact)
    {
        return [
            'id' => (int)$contact->id,
            'first_name' => $contact->first_name,
            'surname' => $contact->surname,
            'phone' => $contact->phone,
            'secondary_phone' => $contact->secondary_phone,
            'email' => $contact->email,
            'image' => secure_asset("storage/images/" . $contact->image),
            'group_id' => $contact->group_id,
            'group_name' => Group::where('id', '=', $contact->group_id)->first()->name
        ];
    }
}
