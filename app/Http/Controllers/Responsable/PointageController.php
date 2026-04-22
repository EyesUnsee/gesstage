<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presence;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function update(Request $request, $id)
    {
        $presence = Presence::findOrFail($id);
        $presence->update([
            'statut' => $request->statut,
            'heure_arrivee' => $request->statut === 'present' ? Carbon::now() : $presence->heure_arrivee,
        ]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Pointage mis à jour');
    }
}
