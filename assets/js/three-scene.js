/**
 * Three.js Background Scene
 * Creates an animated particle grid with magnifying glass effect
 */

class ThreeBackground {
    constructor() {
        this.canvas = document.getElementById('three-canvas');
        if (!this.canvas) return;
        
        this.isTouchDevice = window.matchMedia('(pointer: coarse)').matches;
        
        this.scene = new THREE.Scene();
        this.camera = null;
        this.renderer = null;
        this.particles = null;
        this.grid = null;
        this.mouseParticles = null;
        this.mouseGlow = null;
        
        this.mouse = { x: 0, y: 0, worldX: 0, worldY: 0 };
        this.targetMouse = { x: 0, y: 0 };
        this.time = 0;
        
        // Grid original positions for reset
        this.gridOriginalPositions = null;
        
        this.init();
    }
    
    init() {
        this.renderer = new THREE.WebGLRenderer({
            canvas: this.canvas,
            alpha: true,
            antialias: true
        });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        
        const aspect = window.innerWidth / window.innerHeight;
        const frustumSize = 100;
        this.camera = new THREE.OrthographicCamera(
            frustumSize * aspect / -2,
            frustumSize * aspect / 2,
            frustumSize / 2,
            frustumSize / -2,
            0.1,
            1000
        );
        this.camera.position.z = 50;
        
        this.frustumSize = frustumSize;
        this.aspect = aspect;
        
        this.createParticles();
        this.createGrid();
        
        this.addEventListeners();
        this.animate();
    }
    
    screenToWorld(screenX, screenY) {
        // Convert screen coordinates to world coordinates
        const ndcX = (screenX / window.innerWidth) * 2 - 1;
        const ndcY = -(screenY / window.innerHeight) * 2 + 1;
        
        const vector = new THREE.Vector3(ndcX, ndcY, 0);
        vector.unproject(this.camera);
        
        const dir = vector.sub(this.camera.position).normalize();
        const distance = -this.camera.position.z / dir.z;
        const pos = this.camera.position.clone().add(dir.multiplyScalar(distance));
        
        return { x: pos.x, y: pos.y };
    }
    
    createParticles() {
        const aspect = window.innerWidth / window.innerHeight;
        const spreadX = Math.max(100, 100 * aspect);
        const spreadY = 100;
        const particleCount = this.isTouchDevice ? 200 : 400;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);
        
        const color1 = new THREE.Color(0xffffff);
        const color2 = new THREE.Color(0x666666);
        
        for (let i = 0; i < particleCount; i++) {
            const i3 = i * 3;
            positions[i3] = (Math.random() - 0.5) * spreadX;
            positions[i3 + 1] = (Math.random() - 0.5) * spreadY;
            positions[i3 + 2] = (Math.random() - 0.5) * 20;
            
            const mixedColor = color1.clone().lerp(color2, Math.random());
            colors[i3] = mixedColor.r;
            colors[i3 + 1] = mixedColor.g;
            colors[i3 + 2] = mixedColor.b;
        }
        
        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
        
        const material = new THREE.PointsMaterial({
            size: 0.4,
            vertexColors: true,
            transparent: true,
            opacity: 0.5,
            sizeAttenuation: true
        });
        
        this.particles = new THREE.Points(geometry, material);
        this.scene.add(this.particles);
    }
    
    createGrid() {
        const aspect = window.innerWidth / window.innerHeight;
        const gridSize = Math.max(100, 100 * aspect);
        const divisions = 60;
        const step = gridSize / divisions;
        
        const gridGeometry = new THREE.BufferGeometry();
        const positions = [];
        
        // Horizontal lines
        for (let i = 0; i <= divisions; i++) {
            const y = (i * step) - (gridSize / 2);
            positions.push(-gridSize / 2, y, 0);
            positions.push(gridSize / 2, y, 0);
        }
        
        // Vertical lines
        for (let i = 0; i <= divisions; i++) {
            const x = (i * step) - (gridSize / 2);
            positions.push(x, -gridSize / 2, 0);
            positions.push(x, gridSize / 2, 0);
        }
        
        gridGeometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
        
        // Store original positions for reset each frame
        this.gridOriginalPositions = new Float32Array(positions);
        
        const gridMaterial = new THREE.LineBasicMaterial({
            color: 0x555555,
            transparent: true,
            opacity: 0.15
        });
        
        this.grid = new THREE.LineSegments(gridGeometry, gridMaterial);
        this.scene.add(this.grid);
    }
    
    addEventListeners() {
        if (!this.isTouchDevice) {
            document.addEventListener('mousemove', (e) => {
                // Store screen coordinates
                this.targetMouse.x = e.clientX;
                this.targetMouse.y = e.clientY;
                
                // Convert to world coordinates immediately for accurate tracking
                const worldPos = this.screenToWorld(e.clientX, e.clientY);
                this.mouse.worldX = worldPos.x;
                this.mouse.worldY = worldPos.y;
            });
        }
        
        window.addEventListener('resize', () => {
            const aspect = window.innerWidth / window.innerHeight;
            this.aspect = aspect;
            this.camera.left = -this.frustumSize * aspect / 2;
            this.camera.right = this.frustumSize * aspect / 2;
            this.camera.top = this.frustumSize / 2;
            this.camera.bottom = -this.frustumSize / 2;
            this.camera.updateProjectionMatrix();
            this.renderer.setSize(window.innerWidth, window.innerHeight);
            
            // Recreate grid on resize to maintain proportions
            if (this.grid) {
                this.scene.remove(this.grid);
                this.createGrid();
            }
        });
        
        document.addEventListener('visibilitychange', () => {
            this.isPaused = document.hidden;
        });
    }
    
    // Apply magnifying glass effect to grid
    applyMagnifyingEffect() {
        if (!this.grid || !this.gridOriginalPositions) return;
        
        const positions = this.grid.geometry.attributes.position.array;
        const originalPositions = this.gridOriginalPositions;
        const magnifyRadius = 25; // Radius of magnifying effect
        const maxMagnification = 1.6; // Maximum magnification at center
        
        for (let i = 0; i < positions.length; i += 3) {
            // Get original position
            const origX = originalPositions[i];
            const origY = originalPositions[i + 1];
            
            // Calculate distance from mouse in world space
            const dx = origX - this.mouse.worldX;
            const dy = origY - this.mouse.worldY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            if (dist < magnifyRadius && dist > 0.001) {
                // Calculate magnification factor (stronger near center, fades to 1.0 at edge)
                const normalizedDist = dist / magnifyRadius;
                // Use smooth falloff: 1.0 at edge, maxMagnification at center
                const falloff = 1.0 - (normalizedDist * normalizedDist);
                const magnification = 1.0 + (maxMagnification - 1.0) * falloff;
                
                // Push vertex outward from mouse position
                positions[i] = this.mouse.worldX + dx * magnification;
                positions[i + 1] = this.mouse.worldY + dy * magnification;
                
                // Add slight Z displacement for depth effect
                positions[i + 2] = falloff * 3;
            } else {
                // Reset to original position if outside magnifying radius
                positions[i] = origX;
                positions[i + 1] = origY;
                positions[i + 2] = 0;
            }
        }
        
        this.grid.geometry.attributes.position.needsUpdate = true;
    }
    
    animate() {
        if (this.isPaused) {
            requestAnimationFrame(() => this.animate());
            return;
        }
        
        this.time += 0.016;
        
        // Update mouse smoothing for background parallax
        this.mouse.x += (this.targetMouse.x - this.mouse.x) * 0.08;
        this.mouse.y += (this.targetMouse.y - this.mouse.y) * 0.08;
        
        // Animate background particles
        if (this.particles) {
            this.particles.rotation.z = this.time * 0.02;
            
            const positions = this.particles.geometry.attributes.position.array;
            for (let i = 0; i < positions.length; i += 3) {
                const x = positions[i];
                const y = positions[i + 1];
                positions[i + 2] = Math.sin(this.time * 0.5 + x * 0.05 + y * 0.05) * 2;
            }
            this.particles.geometry.attributes.position.needsUpdate = true;
            
            // Subtle parallax based on mouse
            const ndcX = (this.mouse.x / window.innerWidth) * 2 - 1;
            const ndcY = -(this.mouse.y / window.innerHeight) * 2 + 1;
            this.particles.position.x = ndcX * 5;
            this.particles.position.y = ndcY * 5;
        }
        
        // Apply magnifying glass effect to grid
        if (this.grid) {
            this.grid.rotation.z = Math.sin(this.time * 0.1) * 0.005;
            this.applyMagnifyingEffect();
        }
        

        this.renderer.render(this.scene, this.camera);
        requestAnimationFrame(() => this.animate());
    }
    
    updateTheme(isDark) {
        if (!this.particles) return;
        
        const colors = this.particles.geometry.attributes.color.array;
        const color1 = new THREE.Color(isDark ? 0xffffff : 0x000000);
        const color2 = new THREE.Color(isDark ? 0x666666 : 0x444444);
        
        for (let i = 0; i < colors.length; i += 3) {
            const mixedColor = color1.clone().lerp(color2, Math.random());
            colors[i] = mixedColor.r;
            colors[i + 1] = mixedColor.g;
            colors[i + 2] = mixedColor.b;
        }
        
        this.particles.geometry.attributes.color.needsUpdate = true;
        
        if (this.grid) {
            this.grid.material.color.setHex(isDark ? 0x444444 : 0xcccccc);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.threeBackground = new ThreeBackground();
});
