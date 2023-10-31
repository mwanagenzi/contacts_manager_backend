<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Label;
use App\Transformers\ContactTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Fractalistic\ArraySerializer;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::all();
        logger($contacts);
        return response()->json([
            'success' => true,
            'message' => 'Found ' . $contacts->count() . ' contacts',
            'data' => $contacts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'surname' => 'string',
            'phone' => 'required|string',
            'secondary_phone' => 'string',
            'email' => 'required|email',
            'label' => 'required|string'
        ]);

        $label = Label::query()->where('label', '=', Str::lower($request->label));

        $contact = Contact::create([
            'first_name' => $request->first_name,
            'surname' => $request->surname,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'email' => $request->email,
            'label_id' => $label->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact ' . $contact->firstname . '\'s details stored successfully',
            'data' => json_encode($contact)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Contact\'s data not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Found ' . $contact . '\'s details',
            'data' => json_encode($contact)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'surname' => 'string',
            'phone' => 'required|string',
            'secondary_phone' => 'string',
            'email' => 'required|email',
            'label' => 'required|string'
        ]);

        logger("At update");

        $label = Label::query()->where('label', '=', Str::lower($request->label));

        try {
            $contact = Contact::updateOrCreate([
                'email' => $request->email,
            ], [
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'secondary_phone' => $request->secondary_phone,
                'label_id' => $label->id,
            ]);
        } catch (\Exception $exception) {
            logger($exception);
            return response()->json([
                'success' => false,
                'message' => 'Contact\'s data not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => '' . $contact . '\'s details updated successfully',
            'data' => $contact
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Contact\'s data not found',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Deleted contact\'s details successfully',
        ]);
    }
}
