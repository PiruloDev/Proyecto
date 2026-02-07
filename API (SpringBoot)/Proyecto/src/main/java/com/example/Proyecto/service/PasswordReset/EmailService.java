package com.example.Proyecto.service.PasswordReset;

import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;


@Service
public class EmailService {
    public boolean enviarContrasenaTemporal(String email, String nombreUsuario, String contrasenaTemporal, String tipoUsuario) {
        try {
            // SIMULACIÓN - En producción, aquí iría el código real de envío
            String asunto = "Restablecimiento de Contraseña - Sistema Panadería";
            String cuerpoCorreo = construirCuerpoCorreo(nombreUsuario, contrasenaTemporal, tipoUsuario);
            
            // Log para desarrollo/pruebas
            System.out.println("\n=============== CORREO SIMULADO ===============");
            System.out.println("Fecha: " + LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")));
            System.out.println("Para: " + email);
            System.out.println("Asunto: " + asunto);
            System.out.println("-------------------------------------------");
            System.out.println(cuerpoCorreo);
            System.out.println("===============================================\n");
            
            // TODO: Implementar envío real con JavaMailSender
            // Ejemplo comentado para referencia:
            /*
            MimeMessage mensaje = mailSender.createMimeMessage();
            MimeMessageHelper helper = new MimeMessageHelper(mensaje, true, "UTF-8");
            helper.setTo(email);
            helper.setSubject(asunto);
            helper.setText(cuerpoCorreo, true);
            mailSender.send(mensaje);
            */
            
            return true; // Simular éxito
            
        } catch (Exception e) {
            System.err.println("Error al enviar correo: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
    }
    
    /**
     * Construye el cuerpo del correo electrónico
     */
    private String construirCuerpoCorreo(String nombreUsuario, String contrasenaTemporal, String tipoUsuario) {
        return String.format("""
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
            </head>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                    <h2 style="color: #d35400; text-align: center;">🔐 Restablecimiento de Contraseña</h2>
                    
                    <p>Hola <strong>%s</strong>,</p>
                    
                    <p>Hemos recibido tu solicitud para restablecer la contraseña de tu cuenta.</p>
                    
                    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #d35400; margin: 20px 0;">
                        <p style="margin: 0;"><strong>Tu contraseña temporal es:</strong></p>
                        <p style="font-size: 24px; font-weight: bold; color: #d35400; margin: 10px 0; letter-spacing: 2px;">
                            %s
                        </p>
                    </div>
                    
                    <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #856404;">⚠️ Instrucciones importantes:</h3>
                        <ol style="margin: 10px 0; padding-left: 20px;">
                            <li>Esta contraseña es <strong>temporal y segura</strong></li>
                            <li>Debes <strong>cambiarla inmediatamente</strong> después de iniciar sesión</li>
                            <li>No compartas esta contraseña con nadie</li>
                            <li>Si no solicitaste este cambio, contacta al administrador</li>
                        </ol>
                    </div>
                    
                    <p><strong>Tipo de cuenta:</strong> %s</p>
                    
                    <p>Para iniciar sesión:</p>
                    <ol>
                        <li>Ve a la página de inicio de sesión</li>
                        <li>Ingresa tu email y la contraseña temporal</li>
                        <li>El sistema te pedirá cambiar tu contraseña</li>
                    </ol>
                    
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
                    
                    <p style="font-size: 12px; color: #666; text-align: center;">
                        Este es un correo automático, por favor no respondas.<br>
                        Sistema de Gestión de Panadería - %s
                    </p>
                </div>
            </body>
            </html>
            """, 
            nombreUsuario,
            contrasenaTemporal,
            tipoUsuario,
            LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss"))
        );
    }
}
