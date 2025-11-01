package com.example.pancode.Navigation.NavigationUsers
import androidx.compose.runtime.Composable
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.pancode.Screens.Users.AppScreens
import com.example.pancode.Screens.Users.FirstScreenUsers
import com.example.pancode.Screens.Users.UserProfileScreen

@Composable
fun AppNavigation() {
    val navController = rememberNavController()
    AppScreens.FirstScreenUsers

    NavHost(
        navController = navController,
        startDestination = AppScreens.FirstScreenUsers.route
    ) {
        composable(route = AppScreens.FirstScreenUsers.route) {
            FirstScreenUsers(navController)
        }
        composable(route = AppScreens.SecondScreenUsers.route) {
            UserProfileScreen()
        }
    }
}