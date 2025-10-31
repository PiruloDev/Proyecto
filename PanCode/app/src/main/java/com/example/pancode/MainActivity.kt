package com.example.pancode

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import com.example.pancode.Navigation.NavigationUsers.AppNavigation
import com.example.pancode.ui.theme.PanCodeTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            PanCodeTheme(
                darkTheme = false,
                dynamicColor = false
            ) {
                AppNavigation()
            }
        }
    }
}
