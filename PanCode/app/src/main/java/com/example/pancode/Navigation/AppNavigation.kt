package com.example.pancode.Navigation

import androidx.compose.runtime.Composable
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.pancode.Screens.AppScreens
import com.example.pancode.Screens.FirstScreen
import com.example.pancode.Screens.SecondScreen

@Composable
fun AppNavigation() {
    val navController = rememberNavController()

    NavHost(
        navController = navController,
        startDestination = AppScreens.FirstScreen.route
    ) {
        composable(route = AppScreens.FirstScreen.route) {
            FirstScreen(navController)
        }
        composable(route = AppScreens.SecondScreen.route) {
            SecondScreen(navController)
        }
    }
}
