package com.example.pancode.Api

import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST

interface ApiServiceKotlin {
    @POST("/crear/cliente")
    suspend fun setPersonas(@Body persona: PersonaAPI): Response<Void>

    @GET("/reporte/usuarios")
    suspend fun getPersonas(): Response<List<PersonaAPI>>
}