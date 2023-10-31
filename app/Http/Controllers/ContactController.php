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

        $label = Label::query()->where('label', '=', Str::lower($request->label))->first();

        try {
            $contact = Contact::create([
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'secondary_phone' => $request->secondary_phone,
                'email' => $request->email,
                'label_id' => $label->id,
            ]);

        } catch (\Exception $exception) {
            logger($exception);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store contact data. Kindly check your data format and try again later',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact ' . $contact->firstname . '\'s details stored successfully',
            'data' => $contact
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

        $label = Label::query()->where('label', '=', Str::lower($request->label))->first();

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
            'message' => '' . $contact->first_name . '\'s details updated successfully',
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

    public function filter(Request $request)
    {
        $request->validate([
            'searchTerm' => 'required|string'
        ]);

        try {
            $label = Label::query()->where('label', '=', Str::lower($request->searchTerm))->first();
            $contacts = Contact::query()->select()->where('label_id', '=', $label->id)->get();

        } catch (\Exception $exception) {
            logger($exception);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve contacts with such label'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Found ' . $contacts->count() . ' contacts',
            'data' => $contacts
        ]);

    }
}
