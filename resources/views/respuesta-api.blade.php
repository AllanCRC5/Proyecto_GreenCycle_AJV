
@extends('layouts.app')

@section('title', $titulo . ' | GreenCycle')

@section('content')
    <h1>{{ $titulo }}</h1>

    <p>Ejecuta la operación y consulta la respuesta de la API.</p>

    <p>
        <strong>Método:</strong> {{ $method }}
        <br>
        <strong>Endpoint:</strong> {{ $endpoint }}
    </p>

    <div>
        <label for="token">Token de autenticación:</label>
        <input
            type="password"
            id="token"
            placeholder="Pega aquí tu token si es necesario">
    </div>
    <br>
    <div>
        <label for="body">Body JSON:</label>
        <textarea
            id="body"
            rows="10"
            placeholder='{"name": "Usuario", "email": "usuario@email.com"}'
        ></textarea>
    </div>
    <br>
    <button type="button" id="send-request">
        Enviar petición
    </button>
    <hr>
    <h2>Respuesta del servidor</h2>

    <p id="status">Todavía no se ha enviado ninguna petición.</p>

    <pre id="response"></pre>

    <script>
        //Recibir los datos del backend

        const endpoint = @json($endpoint);
        const method = @json($method);
        const requiresToken = @json($requiresToken);

        console.log(endpoint);
        console.log(method);
        console.log(requiresToken);

        //Se obtienen los elementos del HTML

        const tokenInput = document.getElementById('token');
        const bodyInput = document.getElementById('body');
        const sendButton = document.getElementById('send-request');
        const statusElement = document.getElementById('status');
        const responseElement = document.getElementById('response');

        //El evnetListener de toda la vida
        sendButton.addEventListener('click', async () => {
            const headers = {
                'Accept': 'application/json'
            };

            //comprobar que esté el token cuando se ocupa
            const token = tokenInput.value.trim();

            if (requiresToken && token === '') {
                statusElement.textContent = 'Error: necesitas ingresar un token.';
                responseElement.textContent = '';
                return;
            }

            if (token !== '') {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const options = {
                method: method,
                headers: headers
            };

            //Si es petición post, que se agregue la informacion
            if (method === 'POST') {
                headers['Content-Type'] = 'application/json';

                if (bodyInput.value.trim() !== '') {
                    try {
                        options.body = JSON.stringify(
                            JSON.parse(bodyInput.value)
                        );
                    } catch {
                        statusElement.textContent = 'Error: el JSON no es válido.';
                        responseElement.textContent = '';
                        return;
                    }
                }
            }

            sendButton.disabled = true;
            statusElement.textContent = 'Procesando petición...';

            //Enviar la peticion al backend y esperar respuesta para el front
            try {
                const result = await fetch(endpoint, options);
                const responseText = await result.text();

                let data;

                //convertir respuesta a json
                try {
                    data = JSON.parse(responseText);
                    responseElement.textContent =
                        JSON.stringify(data, null, 4);
                    } 
                catch {
                    data = null;
                    responseElement.textContent = responseText;
                    }

                    //se muestra la respuesta HTTP
                statusElement.textContent =
                    `HTTP ${result.status} - ${result.statusText}`;

                // Si el login o registro devuelve un token,
                // lo colocamos automáticamente en el campo.
                if (data && data.token) {
                    tokenInput.value = data.token;
                }

            } catch (error) {
                statusElement.textContent = 'No se pudo conectar con el servidor.';
                responseElement.textContent = error.message;
            } finally {
                sendButton.disabled = false;
            }
        });
    </script>
@endsection
