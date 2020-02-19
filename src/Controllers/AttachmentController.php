<?php

namespace Cc\Labama\Controllers;

use Cc\Attacent\Facades\Attacent;
use Illuminate\Http\Request;

class AttachmentController extends AdminController
{
    public function __construct()
    {
        Attacent::setUid(admin_guard()->id());
    }

    public function index(Request $request)
    {
        return Attacent::getList(
            $request->input('page', 1),
            $request->input('type', 'image'),
            $request->input('filter', []),
        );
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                try {
                    return succ(Attacent::upload($file));
                } catch (\Exception $e) {
                    return err($e->getMessage());
                }
            }
        }
        return err();
    }

    public function destroy($id)
    {
        try {
            Attacent::delete($id);
            return succ();
        } catch (\Exception $e) {
            return err($e->getMessage());
        }
    }
}
