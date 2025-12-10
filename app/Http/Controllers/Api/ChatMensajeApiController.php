<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMensaje;
use App\Events\MensajeChatCreado;

class ChatMensajeApiController extends Controller
{
     // GET /api/chat-mensajes?voluntario_id=XX
    public function index(Request $request)
    {
        $request->validate([
            'voluntario_id' => 'required|integer|exists:usuario,id_usuario',
        ]);

        $mensajes = ChatMensaje::where('voluntario_id', $request->voluntario_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $mensajes,
        ]);
    }

    // POST /api/chat-mensajes
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voluntario_id' => 'required|integer|exists:usuario,id_usuario',
            'de'            => 'required|in:voluntario,admin',
            'texto'         => 'required|string|max:1000',
        ]);

        // 🔴 FORZAR A INTEGER para consistencia
        $validated['voluntario_id'] = (int) $validated['voluntario_id'];

        // Trazabilidad API Gateway
        $validated['ci_voluntario_accion'] = \App\Models\User::where('id_usuario', $validated['voluntario_id'])->value('ci');

        $mensaje = ChatMensaje::create($validated);
        $mensaje->load('voluntario');

        MensajeChatCreado::dispatch($mensaje);

        return response()->json([
            'success' => true,
            'data'    => $mensaje,
        ], 201);
    }
}