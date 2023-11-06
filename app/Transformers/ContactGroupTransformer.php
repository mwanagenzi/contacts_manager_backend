<?php

namespace App\Transformers;

use App\Models\Contact;
use App\Models\ContactGroup;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ContactGroupTransformer extends TransformerAbstract
{
    public function transform(ContactGroup $contactGroup)
    {
        $contacts = Contact::query()->where('id', '=', $contactGroup->contact_id)->get();
        $group = $contactGroup->group;

        return [
            'id' => (int)$contactGroup->id,
            'group_id' => (int)$group->id,
            'group_name' => $group->name,
            'contacts' => fractal($contacts, ContactTransformer::class, ArraySerializer::class)->withResourceName('contacts')
        ];
    }
}
