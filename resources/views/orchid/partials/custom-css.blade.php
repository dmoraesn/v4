<style>
    /**
     * Customização do Painel Orchid - BuscaLeis
     * Foco: Ajuste de componentes de imagem (Picture/Cropper)
     */

    /* Esconde o preview automático que pode quebrar o layout do form */
    .picture-preview, 
    .cropper-preview {
        display: none !important;
    }

    /* Otimização de visualização para inputs de arquivo */
    .form-group.picture-wrapper {
        border: 1px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .form-group.picture-wrapper:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
</style>