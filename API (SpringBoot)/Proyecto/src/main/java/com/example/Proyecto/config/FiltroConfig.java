package com.example.Proyecto.config;

import com.example.Proyecto.security.JwtFiltro;
import com.example.Proyecto.security.JwtUtilidad;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class FiltroConfig {

    @Autowired
    private JwtUtilidad jwtUtilidad;

    @Bean
    public FilterRegistrationBean<JwtFiltro> jwtFilterRegistration() {
        FilterRegistrationBean<JwtFiltro> registroFiltro = new FilterRegistrationBean<>();
        JwtFiltro jwtFiltro = new JwtFiltro();
        jwtFiltro.setJwtUtilidad(jwtUtilidad);
        registroFiltro.setFilter(jwtFiltro);
        registroFiltro.addUrlPatterns("/usuarios/*");
        registroFiltro.addUrlPatterns("/productos/*");
        return registroFiltro;
    }
}