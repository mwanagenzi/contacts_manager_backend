<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Group;
use App\Transformers\GroupTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Fractal\Serializer\ArraySerializer;

class ContactGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //todo: retrieve all groups along with their contacts
        $groups = Group::all();
        return response()->json([
            'success' => true,
            'message' => 'Found ' . $groups->count() . ' contact groups',
            'group_count' => $groups->count(),
//            'data' => fractal($contactGroups, ContactGroupTransformer::class, ArraySerializer::class)
//            'data' => $contactGroups
            'data' => fractal($groups, GroupTransformer::class, ArraySerializer::class)->withResourceName('data')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //todo: store a group of contact ids into one group
        try {
            $request->validate([
                'name' => 'required|string',
                'contact_ids' => 'required|array'
            ]);
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store the data. Check your data format then try again.'
            ]);
        }

        $group = Group::create([
            'name' => Str::lower($request->name),
        ]);

        try {
            foreach ($request->contact_ids as $contact_id) {
                ContactGroup::create([
                    'group_id' => $group->id,
                    'contact_id' => $contact_id
                ]);
            }
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store the data. Try again later.'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Contact group ' . $group->name . '\'s data stored successfully.'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
                'contact_ids' => 'required|array'
            ]);
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store the data. Check your data format then try again.'
            ]);
        }

        try {
            DB::beginTransaction();
            foreach ($request->contact_ids as $contact_id) {
                ContactGroup::where('contact_id', '=', $contact_id)->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to update the group data. Check the server log.'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Contact group data updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //todo: delete the group only
        try {

            $group = Group::find($id);
            if ($group) {
                DB::beginTransaction();

                ContactGroup::where('group_id', '=', $group->id)->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Group details deleted successfully.'
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact\'s data not found',
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete the group data. Try again later.'
            ]);
        }

    }
}
