<?php

namespace App\Transformers;

use App\Models\Contact;
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
        ];
    }
}
