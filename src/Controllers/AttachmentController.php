<?php

namespace Cc\Labama\Controllers;

use Cc\Attacent\Facades\Attacent;
use Cc\Labama\Facades\Auth;
use Illuminate\Http\Request;

class AttachmentController extends BaseController
{
    public function __construct()
    {
        Attacent::setUid(Auth::id())
            ->setPageSize(10)
            ->setPrefix(LABAMA_ENTRY);
    }

    public function index(Request $request)
    {
        return succ(Attacent::getList(
            $request->input('page', 1),
            $request->input('type', 'image'),
            $request->input('filter', []),
        ));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                return succ(Attacent::upload($file));
            }
        }
        return err('The file does not exist or invalid');
    }

    public function destroy($id)
    {
        Attacent::delete($id);
        return succ();
    }
}
