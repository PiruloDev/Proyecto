package com.example.pancode.Api

import retrofit2.Response
import retrofit2.http.GET

interface ApiServiceDogs {
    @GET("breeds/image/random")
    suspend fun getRandomDogImage(): Response<DogResponse>
}
data class DogResponse(
    val message: String,
    val status: String
)