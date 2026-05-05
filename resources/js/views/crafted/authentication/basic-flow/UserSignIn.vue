<template>
  <div class="w-lg-500px p-10">
    <VForm
      class="form w-100"
      id="kt_login_signin_form"
      @submit="onSubmitLogin"
      :validation-schema="login"
    >
      <!--begin::Heading-->
      <div class="text-center mb-10">
        <h1 class="text-gray-900 mb-3">Sign In</h1>
        <div class="text-gray-500 fw-semibold fs-4">
          New Here?
          <router-link to="/sign-up" class="link-primary fw-bold">
            Create an Account
          </router-link>
        </div>
      </div>
      <!--end::Heading-->

      <!--begin::Input Email-->
      <div class="fv-row mb-10">
        <label class="form-label fs-6 fw-bold text-gray-900">Email</label>
        <Field
          tabindex="1"
          class="form-control form-control-lg form-control-solid"
          type="text"
          name="email"
          autocomplete="off"
        />
        <div class="fv-plugins-message-container">
          <div class="fv-help-block">
            <ErrorMessage name="email" />
          </div>
        </div>
      </div>
      <!--end::Input Email-->

      <!--begin::Input Password-->
      <div class="fv-row mb-10">
        <div class="d-flex flex-stack mb-2">
          <label class="form-label fw-bold text-gray-900 fs-6 mb-0">Password</label>
          <router-link to="/password-reset" class="link-primary fs-6 fw-bold">
            Forgot Password ?
          </router-link>
        </div>
        <Field
          tabindex="2"
          class="form-control form-control-lg form-control-solid"
          type="password"
          name="password"
          autocomplete="off"
        />
        <div class="fv-plugins-message-container">
          <div class="fv-help-block">
            <ErrorMessage name="password" />
          </div>
        </div>
      </div>
      <!--end::Input Password-->

      <!--begin::Actions-->
      <div class="text-center">
        <button
          tabindex="3"
          type="submit"
          ref="submitButton"
          id="kt_sign_in_submit"
          class="btn btn-lg btn-primary w-100 mb-5"
        >
          <span class="indicator-label">Continue</span>
          <span class="indicator-progress">
            Please wait...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
          </span>
        </button>

        <!--begin::Separator-->
        <div class="text-center text-muted text-uppercase fw-bold mb-5">or</div>
        <!--end::Separator-->

        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
          <img
            alt="Logo"
            :src="getAssetPath('media/svg/brand-logos/google-icon.svg')"
            class="h-20px me-3"
          />
          Continue with Google
        </a>

        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
          <img
            alt="Logo"
            :src="getAssetPath('media/svg/brand-logos/facebook-4.svg')"
            class="h-20px me-3"
          />
          Continue with Facebook
        </a>

        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100">
          <img
            alt="Logo"
            :src="getAssetPath('media/svg/brand-logos/apple-black.svg')"
            class="h-20px me-3"
          />
          Continue with Apple
        </a>

        <!--begin::Admin Link-->
        <div class="text-center mt-5">
          <router-link to="/admin/login" class="link-danger fs-6 fw-bold">
            Login sebagai Admin?
          </router-link>
        </div>
        <!--end::Admin Link-->
      </div>
      <!--end::Actions-->
    </VForm>
  </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import { useAuthStore, type User } from "@/stores/auth";
import { useRouter } from "vue-router";
import Swal from "sweetalert2/dist/sweetalert2.js";
import * as Yup from "yup";

export default defineComponent({
  name: "sign-in",
  components: {
    Field,
    VForm,
    ErrorMessage,
  },
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const submitButton = ref<HTMLButtonElement | null>(null);

    const login = Yup.object().shape({
      email: Yup.string().email().required().label("Email"),
      password: Yup.string().min(4).required().label("Password"),
    });

    const onSubmitLogin = async (values: any) => {
      values = values as User;
      store.logout();

      if (submitButton.value) {
        submitButton.value!.disabled = true;
        submitButton.value.setAttribute("data-kt-indicator", "on");
      }

      // Pakai loginUser bukan login biasa
      await store.loginUser(values);
      const error = Object.values(store.errors);

      if (error.length === 0) {
        Swal.fire({
          text: "You have successfully logged in!",
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok, got it!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-primary",
          },
        }).then(() => {
          // Redirect ke user dashboard
          router.push({ name: "user-dashboard" });
        });
      } else {
        Swal.fire({
          text: error[0] as string,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "Try again!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-danger",
          },
        }).then(() => {
          store.errors = {};
        });
      }

      submitButton.value?.removeAttribute("data-kt-indicator");
      submitButton.value!.disabled = false;
    };

    return {
      onSubmitLogin,
      login,
      submitButton,
      getAssetPath,
    };
  },
});
</script>
