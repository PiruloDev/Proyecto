package com.example.pancode.Screens.Users

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.pancode.R

@Composable
fun UserProfileScreen() {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFE3C4B9))
            .verticalScroll(rememberScrollState())
    ) {
        Spacer(modifier = Modifier.height(25.dp))

        // Header con botones de navegación
        TopBar()

        // Contenido del perfil
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(modifier = Modifier.height(16.dp))

            // Imagen de perfil
            MyImage2()

            Spacer(modifier = Modifier.height(24.dp))

            // Nombre
            Text(
                text = "Damian Avila",
                fontSize = 32.sp,
                fontWeight = FontWeight.Bold,
                color = Color(0xFF1F1F1F)
            )

            Spacer(modifier = Modifier.height(8.dp))

            // Rol
            Text(
                text = "Administrador",
                fontSize = 18.sp,
                color = Color.Red,
                fontWeight = FontWeight.Bold,
                textDecoration = TextDecoration.Underline
            )

            Spacer(modifier = Modifier.height(32.dp))

            // Menú de opciones
            MenuOptions()

            Spacer(modifier = Modifier.height(24.dp))

            // Botón de logout
            LogoutButton()

            Spacer(modifier = Modifier.height(24.dp))
        }
    }
}

@Composable
fun TopBar() {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(16.dp),
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        IconButton(onClick = { /* Acción de retroceso */ }) {
            Icon(
                imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                contentDescription = "Atrás",
                tint = Color(0xFF1F1F1F)
            )
        }

        IconButton(onClick = { /* Acción de editar */ }) {
            Icon(
                imageVector = Icons.Default.Edit,
                contentDescription = "Editar",
                tint = Color(0xFF1F1F1F)
            )
        }
    }
}

@Composable
fun MyImage2() {
    Image(
        painter = painterResource(R.drawable.chefadmin),
        contentDescription = "Imagen de perfil",
        contentScale = ContentScale.Crop,
        modifier = Modifier
            .size(120.dp)
            .clip(CircleShape)
            .border(3.dp, Color.White, CircleShape)
    )
}

@Composable
fun MenuCard(
    icon: ImageVector,
    text: String,
    color: Color,
    onClick: () -> Unit = {}
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .height(70.dp),
        colors = CardDefaults.cardColors(containerColor = color),
        shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 4.dp),
        onClick = onClick
    ) {
        Row(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(
                imageVector = icon,
                contentDescription = text,
                tint = Color.White,
                modifier = Modifier.size(28.dp)
            )
            Spacer(modifier = Modifier.width(16.dp))
            Text(
                text = text,
                color = Color.White,
                fontSize = 16.sp,
                fontWeight = FontWeight.SemiBold
            )
        }
    }
}

@Composable
fun MenuOptions() {
    Column(
        modifier = Modifier.fillMaxWidth(),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        MenuCard(
            icon = Icons.Default.FreeBreakfast,
            text = "Total de Productos",
            color = Color(0xFFEA9437)
        )

        MenuCard(
            icon = Icons.Default.Inventory,
            text = "Total de Ingredientes",
            color = Color(0xFF10B981)
        )

        MenuCard(
            icon = Icons.Default.Group,
            text = "Mis Empleados",
            color = Color(0xFFF59E0B)
        )

        MenuCard(
            icon = Icons.Default.PersonOutline,
            text = "Mis Clientes",
            color = Color(0xFFEF4444)
        )

        MenuCard(
            icon = Icons.Default.Settings,
            text = "Configuración de Cuenta",
            color = Color(0xFF6366F1)
        )
    }
}

@Composable
fun LogoutButton() {
    Button(
        onClick = { /* Acción de logout */ },)
    {
        Icon(
            imageVector = Icons.Default.Logout,
            contentDescription = "Cerrar sesión",
            tint = Color.White,
            modifier = Modifier.size(24.dp)
        )

        Spacer(modifier = Modifier.width(12.dp))

        Text(
            text = "Cerrar Sesión",
            fontSize = 16.sp,
            color = Color.White,
            fontWeight = FontWeight.Bold
        )
    }
}

@Preview(showBackground = true, showSystemUi = true)
@Composable
fun UserProfileScreenPreview() {
    MaterialTheme {
        UserProfileScreen()
    }
}