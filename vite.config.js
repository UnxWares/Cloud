import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from "@sveltejs/vite-plugin-svelte";
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
	    postcss: [
            	autoprefixer()
            ],
        }),
	svelte({
		compilerOptions: {
			compatibility: {
				componentApi: 4
			}
		}
	}),
    ],
    css: {
        	preprocessorOptions: {
        	scss: {},
    	},
    },
});