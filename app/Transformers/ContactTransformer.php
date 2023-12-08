<?php

namespace App\Transformers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Group;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    public function transform(Contact $contact)
    {
        $contact_group = ContactGroup::where('contact_id', '=', $contact->id)->first();
        return [
            'id' => (int)$contact->id,
            'first_name' => $contact->first_name,
            'surname' => $contact->surname,
            'phone' => $contact->phone,
            'secondary_phone' => $contact->secondary_phone,
            'email' => $contact->email,
            'image' => secure_asset("storage/images/" . $contact->image),
            'group_id' => $contact_group->group_id ?? 0,
            'group_name' => $contact_group == null ? '' : Group::where('id', '=', $contact_group->group_id)->first()->name,
        ];
    }
}
