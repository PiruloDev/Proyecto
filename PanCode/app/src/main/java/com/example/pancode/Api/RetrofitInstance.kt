package com.example.pancode.Api

import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object RetrofitInstance {
    private const val BASE_URL_DOGS = "https://dog.ceo/api/"
    private const val BASE_URL_APIKOTLIN = "http://192.168.20.48:8080/"

    val api: ApiServiceDogs by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL_DOGS)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiServiceDogs::class.java)
    }

    val api2kotlin: ApiServiceKotlin by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL_APIKOTLIN)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiServiceKotlin::class.java)
    }
}