<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use App\Models\Group;
use App\Transformers\ContactTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //todo: retrieve all groups along with their contacts
        $contactGroups = ContactGroup::all()->groupBy('group_id');
        return response()->json([
            'success' => true,
            'message' => 'Found ' . $contactGroups->count() . ' contact groups',
            'data' => $contactGroups
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
        //todo: store a new collection of contacts in an existing group
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
            foreach ($request->contact_ids as $contact_id) {
                ContactGroup::updateOrCreate([
                    'group_id' => $request->group_id,
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
            'message' => 'Contact group data updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
            ]);
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete the data. Check your input format then try again.'
            ]);
        }
        //todo: delete the group only
        try {
            DB::table('contact_groups')
                ->where('group_id', '=', $request->group_id)
                ->delete();
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to store the data. Try again later.'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Group details deleted successfully'
        ]);
    }
}
