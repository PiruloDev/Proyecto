package com.example.pancode.Screens.Users

sealed class AppScreens(val route: String) {
    object FirstScreenUsers: AppScreens("first_screen")
    object SecondScreenUsers: AppScreens("second_screen")
}