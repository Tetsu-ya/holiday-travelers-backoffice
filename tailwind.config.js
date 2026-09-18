/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#163B6D',    // Deep Navy Blue
                secondary: '#F59B45',  // Sunset Orange
                accent: '#6FA9E6',     // Sky Blue
                background: '#F8FAFC', // Snow White
                card: '#FFFFFF',       // White
                border: '#E5E7EB',     // Light Gray
                success: '#22C55E',    // Green
                warning: '#FBBF24',    // Amber
                error: '#EF4444',      // Red
            },
            fontFamily: {
                heading: ['Poppins', 'sans-serif'],
                button: ['Poppins', 'sans-serif'],
                body: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
