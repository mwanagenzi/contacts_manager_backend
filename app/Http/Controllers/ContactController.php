<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Group;
use App\Models\Label;
use App\Transformers\ContactTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'data' => fractal($contacts, ContactTransformer::class, ArraySerializer::class)->withResourceName('data'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string',
                'surname' => 'string',
                'phone' => 'required|string',
                'secondary_phone' => 'string|nullable',
                'email' => 'required|email',
                'image' => 'image|nullable|max:1999',
                'group_name' => 'string|nullable'
            ]);
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store contact data. Kindly check your data format and check server log, then try again',
            ]);
        }


        try {
            DB::beginTransaction();

            $group_id = Group::where('name', '=', Str::lower($request->group_name))->first()->id ??
                Group::create([
                    'name' => Str::lower($request->group_name)
                ])->id;
            $image = self::setUpTheFrontImages($request);

            $contact = Contact::create([
                'phone' => $request->phone,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'secondary_phone' => $request->secondary_phone,
                'label_id' => fake()->randomDigitNotZero(), //default to "work" label
                'image' => $image,
            ]);

            ContactGroup::create([
                'group_id' => $group_id,
                'contact_id' => $contact->id
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            logger($exception);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store contact data. Check server log.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact ' . $contact->firstname . '\'s details stored successfully',
//            'data' => $contact todo:Do you need this data?
        ]);
    }

    public static function setUpTheFrontImages(Request $request): ?string
    {
        if ($request->has('image')) {

            logger("has image");

            if (preg_match('/^data:image\/(\w+);base64,/', $request->image, $matches)) {
                logger("it's a base64 string image...");
                $image_type = Str::lower($matches[1]);

                // Map the image type to a file extension.
                $extensions = [
                    'jpeg' => 'jpg',
                    'jpg' => 'jpg',
                    'png' => 'png',
                    // Add more image types and extensions as needed.
                ];
                $extension = $extensions[$image_type];

                if (empty($extension)) {
                    $extension = 'png';
                }

                $image = Str::slug($request->first_name, '_') . '.' . $extension;
                $image_data = base64_decode(substr($request->image, strpos($request->image, ',') + 1));
                Storage::disk('public')->put('images/' . $image, $image_data);

                return $image;
            }

            if ($request->hasFile('image')) {
                logger("it's an image...");

                $extension = $request->file('image')->extension();
                $image = Str::slug($request->first_name, '_') . '.' . $extension;
                $request->file('image')->storeAs(
                    'images',
                    $image,
                    'public'
                );

                return $image;
            }
        }

        logger("neither base 64 nor image file...");

        return "no_image.png";

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
            'data' => fractal($contact, ContactTransformer::class, ArraySerializer::class)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|int',
            'first_name' => 'required|string',
            'surname' => 'string',
            'phone' => 'required|string',
            'secondary_phone' => 'string|nullable',
            'email' => 'required|email',
            'group_name' => 'string|nullable',
            'image' => 'image|nullable|max:1999'
        ]);

        $group = Group::where('name', '=', Str::lower($request->group_name))->first();

        if ($group == null) {
            $group_id = Group::create([
                'name' => Str::lower($request->group_name)
            ])->id;
            ContactGroup::create([
                'group_id' => $group_id,
                'contact_id' => Contact::where('id', '=', $request->id)->id
            ]);
        }

        $image = self::setUpTheFrontImages($request);

        try {
            $contact = Contact::updateOrCreate([
                'id' => $request->id,
            ], [
                'email' => $request->email,
                'phone' => $request->phone,
                'first_name' => $request->first_name,
                'surname' => $request->surname ?? '',
                'secondary_phone' => $request->secondary_phone,
                'image' => $image,
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
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::find($id);
            if ($contact) {
                DB::beginTransaction();

                $contact_group = ContactGroup::where('contact_id', '=', $contact->id)->first();
                Group::where('id', '=', $contact_group->id)->delete();
                $contact_group->delete();
                $contact->delete();

                DB::commit();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact\'s data not found',
                ]);
            }
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete the contact. Check server log.',
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
