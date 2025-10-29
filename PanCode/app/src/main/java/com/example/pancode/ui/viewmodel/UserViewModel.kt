package com.example.pancode.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.pancode.Api.PersonaAPI
import com.example.pancode.Api.RetrofitInstance
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class UserViewModel : ViewModel() {

    private val _personas = MutableStateFlow<List<String>>(emptyList())
    val personas: StateFlow<List<String>> = _personas

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    fun obtenerPersonas() {
        viewModelScope.launch {
            try {
                val response = RetrofitInstance.api2kotlin.getPersonas()
                if (response.isSuccessful) {
                    val data = response.body()
                    if (!data.isNullOrEmpty()) {
                        _personas.value = data.map { persona ->
                            "Nombre: ${persona.nombre}\nEmail: ${persona.email}\nTeléfono: ${persona.telefono}"
                        }
                    } else {
                        _error.value = "No se encontraron datos."
                    }
                } else {
                    _error.value = "Error en la respuesta del servidor: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "Error de conexión: ${e.message}"
            }
        }
    }
}